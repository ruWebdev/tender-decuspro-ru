<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekParseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Исходный текст для разбора.
     */
    public string $raw_text;

    /**
     * Идентификатор задачи.
     */
    public string $jobId;

    public function __construct(string $raw_text, string $jobId)
    {
        $this->raw_text = $raw_text;
        $this->jobId = $jobId;
    }

    /**
     * Выполнение задачи.
     */
    public function handle(): void
    {
        try {
            $token = config('services.deepseek.token');
            $baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');

            if (! $token) {
                Log::warning('DeepSeekParseJob: отсутствует API ключ DeepSeek', [
                    'job_id' => $this->jobId,
                ]);

                Cache::put("deepseek_result_{$this->jobId}", [], 3600);

                return;
            }

            $prompt = "Parse the following text and extract items with their quantities. Return a JSON array with objects containing 'title', 'quantity', and 'unit' fields.\n\n" . $this->raw_text;

            $response = Http::withToken($token)
                ->timeout(30)
                ->post("{$baseUrl}/v1/chat/completions", [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'max_tokens' => 1024,
                ]);

            if (! $response->successful()) {
                Log::warning('DeepSeekParseJob: неуспешный ответ DeepSeek', [
                    'job_id' => $this->jobId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                Cache::put("deepseek_result_{$this->jobId}", [], 3600);

                return;
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content)) {
                Log::warning('DeepSeekParseJob: неожиданный формат ответа', [
                    'job_id' => $this->jobId,
                ]);

                Cache::put("deepseek_result_{$this->jobId}", [], 3600);

                return;
            }

            // Попытаемся извлечь JSON из ответа
            $jsonMatch = preg_match('/\[.*\]/s', $content, $matches);

            if ($jsonMatch && isset($matches[0])) {
                $parsedItems = json_decode($matches[0], true);

                if (is_array($parsedItems)) {
                    Cache::put("deepseek_result_{$this->jobId}", $parsedItems, 3600);

                    return;
                }
            }

            Log::warning('DeepSeekParseJob: не удалось распарсить JSON из ответа', [
                'job_id' => $this->jobId,
                'content' => $content,
            ]);

            Cache::put("deepseek_result_{$this->jobId}", [], 3600);
        } catch (\Throwable $e) {
            Log::error('DeepSeekParseJob: ошибка при выполнении', [
                'job_id' => $this->jobId,
                'message' => $e->getMessage(),
            ]);

            Cache::put("deepseek_result_{$this->jobId}", [], 3600);

            throw $e;
        }
    }

    /**
     * Обработка провала задачи.
     */
    public function fail(\Throwable $exception): void
    {
        Log::error('DeepSeekParseJob: задача завершилась с ошибкой', [
            'job_id' => $this->jobId,
            'message' => $exception->getMessage(),
        ]);
    }
}
