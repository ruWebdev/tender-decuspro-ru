<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TenderGeneratorService
{
    public function generate(?User $customer = null): ?Tender
    {
        if (! $customer) {
            $customer = User::role(User::ROLE_CUSTOMER)
                ->orderBy('id')
                ->first();
        }

        if (! $customer) {
            Log::warning('TenderGeneratorService: не найден заказчик для автогенерации тендера');

            return null;
        }

        $token = config('services.deepseek.token');
        $baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');

        if (! $token) {
            Log::warning('TenderGeneratorService: отсутствует токен DeepSeek API');

            return null;
        }

        $prompt = $this->buildPrompt();

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
                    'max_tokens' => 1024,
                ]);

            if ($response->failed()) {
                Log::warning('TenderGeneratorService: неуспешный ответ DeepSeek', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || $content === '') {
                Log::warning('TenderGeneratorService: пустой или некорректный контент ответа DeepSeek');

                return null;
            }

            $data = $this->extractJson($content);

            if (! is_array($data)) {
                Log::warning('TenderGeneratorService: не удалось распарсить JSON из ответа DeepSeek', [
                    'content' => $content,
                ]);

                return null;
            }

            return $this->createTenderFromArray($customer, $data);
        } catch (\Throwable $e) {
            Log::error('TenderGeneratorService: ошибка при генерации тендера', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function buildPrompt(): string
    {
        $custom = Setting::get('tender_prompt');
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }

        return <<<'PROMPT'
You are generating a new tender (procurement) for an online tender platform.

Topic: IT hardware and related IT equipment (servers, workstations, laptops, network equipment, storage systems, peripherals, etc.).

Generate one realistic tender created by a corporate customer.
The tender must describe a specific procurement, not a template or an instruction.

Business rules:
- Use business style, without marketing hype.
- Do not use markdown, bullet lists or numbering.
- The scope must be only IT equipment and related services.

Output format:
Return STRICTLY one JSON object, without any surrounding text, explanations or code fences.

The JSON object MUST have the following structure and fields:
{
  "title": "string, Russian name of the tender, 5-12 words",
  "description_ru": "string, detailed Russian description (3-6 sentences)",
  "description_en": "string, English translation of the same description (3-6 sentences)",
  "description_cn": "string, Chinese (Simplified) translation of the same description (3-6 sentences)",
  "hidden_comment": "string, short internal comment for the customer in Russian, 1-3 sentences",
  "valid_until_days_from_now": integer, number of days from today until the tender expires (from 5 to 30),
  "items": [
    {
      "title_ru": "string, Russian name of the item",
      "title_en": "string, English name of the item",
      "title_cn": "string, Chinese (Simplified) name of the item",
      "quantity": number, required quantity greater than 0,
      "unit": "string, Russian unit of measurement, for example: 'шт.', 'компл.', 'пар.'"
    }
  ]
}

Additional rules for the "items" array:
- Generate from 3 to 10 items.
- All numeric values must be valid JSON numbers (no text, no units inside quantity).
- All text values must be plain strings without line breaks at the beginning or end.

Important:
- Do not include any other fields except the ones described above.
- Do not include comments, explanations, markdown or code examples.
- The final answer MUST be valid JSON that can be parsed by a JSON parser.
PROMPT;
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

    private function createTenderFromArray(User $customer, array $data): ?Tender
    {
        $title = isset($data['title']) ? (string) $data['title'] : '';

        if ($title === '') {
            return null;
        }

        $descriptionRu = isset($data['description_ru']) ? (string) $data['description_ru'] : '';
        $descriptionEn = isset($data['description_en']) ? (string) $data['description_en'] : '';
        $descriptionCn = isset($data['description_cn']) ? (string) $data['description_cn'] : '';
        $hiddenComment = isset($data['hidden_comment']) ? (string) $data['hidden_comment'] : null;

        $days = isset($data['valid_until_days_from_now'])
            ? (int) $data['valid_until_days_from_now']
            : 7;

        if ($days < 1) {
            $days = 7;
        }

        if ($days > 60) {
            $days = 60;
        }

        $tender = Tender::create([
            'customer_id' => $customer->id,
            'title' => $title,
            'description' => $descriptionRu !== '' ? $descriptionRu : null,
            'description_en' => $descriptionEn !== '' ? $descriptionEn : null,
            'description_cn' => $descriptionCn !== '' ? $descriptionCn : null,
            'hidden_comment' => $hiddenComment !== '' ? $hiddenComment : null,
            'valid_until' => now()->addDays($days),
            'status' => 'open',
        ]);

        $items = [];

        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $titleRu = isset($row['title_ru']) ? trim((string) $row['title_ru']) : '';

                if ($titleRu === '') {
                    continue;
                }

                $quantityRaw = $row['quantity'] ?? null;
                $quantity = is_numeric($quantityRaw) ? (float) $quantityRaw : 0.0;

                if ($quantity <= 0) {
                    $quantity = 1.0;
                }

                $unit = isset($row['unit']) ? (string) $row['unit'] : null;

                $items[] = [
                    'title' => $titleRu,
                    'name_en' => isset($row['title_en']) ? (string) $row['title_en'] : null,
                    'name_cn' => isset($row['title_cn']) ? (string) $row['title_cn'] : null,
                    'quantity' => $quantity,
                    'unit' => $unit !== '' ? $unit : null,
                    'position_index' => (int) $index,
                ];
            }
        }

        if ($items !== []) {
            $tender->items()->createMany($items);
        }

        return $tender->load('items');
    }
}
