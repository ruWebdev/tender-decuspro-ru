<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateTenderRuSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Tender $tender) {}

    public function handle(): void
    {
        $tender = $this->tender->fresh();

        if (! $tender) {
            return;
        }

        $tender->loadMissing('items');

        $token = Setting::get('deepseek_api_key') ?? config('services.deepseek.token');
        $baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');

        if (! $token) {
            Log::warning('GenerateTenderRuSummaryJob: отсутствует токен DeepSeek API', [
                'tender_id' => $tender->id,
            ]);

            return;
        }

        $itemsText = $tender->items
            ->values()
            ->map(function ($item) {
                $title = (string) $item->getRawOriginal('title');
                $qty = (string) $item->getRawOriginal('quantity');
                $unit = (string) ($item->getRawOriginal('unit') ?? '');

                return trim($title . ' — ' . $qty . ($unit !== '' ? (' ' . $unit) : ''));
            })
            ->filter()
            ->implode("\n");

        $prompt = "Сформируй заголовок и короткое описание тендера на русском языке по списку позиций.\n" .
            "Требования:\n" .
            "1) Заголовок: 5-12 слов, деловой стиль.\n" .
            "2) Короткое описание: 2-4 предложения, без списков и без разметки.\n" .
            "3) Верни строго JSON без пояснений: {\"title\":\"...\",\"description\":\"...\"}.\n\n" .
            "Позиции:\n" . $itemsText;

        try {
            $response = Http::withToken($token)
                ->timeout(45)
                ->post("{$baseUrl}/v1/chat/completions", [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'max_tokens' => 512,
                ]);

            if ($response->failed()) {
                Log::warning('GenerateTenderRuSummaryJob: неуспешный ответ DeepSeek', [
                    'tender_id' => $tender->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return;
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || $content === '') {
                return;
            }

            $data = $this->extractJson($content);

            if (! is_array($data)) {
                return;
            }

            $title = isset($data['title']) ? trim((string) $data['title']) : '';
            $description = isset($data['description']) ? trim((string) $data['description']) : '';

            if ($title !== '') {
                $tender->title = $title;
            }

            if ($description !== '') {
                $tender->description = $description;
            }

            $tender->save();

            TranslateTenderJob::dispatch($tender);
        } catch (\Throwable $e) {
            Log::error('GenerateTenderRuSummaryJob: ошибка при генерации', [
                'tender_id' => $tender->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function extractJson(string $content): ?array
    {
        $content = trim($content);

        if ($content === '') {
            return null;
        }

        if ($content[0] === '{') {
            $decoded = json_decode($content, true);

            return is_array($decoded) ? $decoded : null;
        }

        if (preg_match('/\{.*\}/s', $content, $matches) && isset($matches[0])) {
            $decoded = json_decode($matches[0], true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }
}
