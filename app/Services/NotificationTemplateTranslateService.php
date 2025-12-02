<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для перевода текстов шаблонов уведомлений на три языка через DeepSeek.
 */
class NotificationTemplateTranslateService
{
    /**
     * Перевести тексты шаблона на русский, английский и китайский языки.
     *
     * @return array{body_ru: string, body_en: string, body_cn: string}|null
     */
    public function translate(?string $bodyRu, ?string $bodyEn, ?string $bodyCn): ?array
    {
        $token = config('services.deepseek.token');
        $baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');

        if (! $token) {
            Log::warning('NotificationTemplateTranslateService: отсутствует API ключ DeepSeek');

            return null;
        }

        $input = [
            'ru' => $bodyRu,
            'en' => $bodyEn,
            'cn' => $bodyCn,
        ];

        $inputJson = json_encode($input, JSON_UNESCAPED_UNICODE);

        $prompt = "You are a professional translator for email notification templates of a B2B procurement platform.\n\n" .
            "You receive a JSON object named 'input' with optional text variants in Russian (ru), English (en) and Chinese (cn) for the SAME message.\n" .
            "Your task:\n" .
            "1. Produce a consistent, polished version of the same message in all three languages.\n" .
            "2. Use a polite, formal business tone suitable for an email notification on a B2B tender platform.\n" .
            "3. Always output all three languages, even if some input fields are null or empty.\n" .
            "4. Prefer Russian as the main source if it is provided; otherwise prefer English; otherwise Chinese.\n\n" .
            "Input JSON in UTF-8:\n" . $inputJson . "\n\n" .
            "Output requirements:\n" .
            "- Return ONLY a valid JSON object.\n" .
            "- The object MUST have exactly three string fields: 'ru', 'en', 'cn'.\n" .
            "- Do not add comments, explanations, markdown or backticks.\n\n" .
            "Example of the expected output format (without comments):\n" .
            '{"ru":"...","en":"...","cn":"..."}';

        try {
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
                    'max_tokens' => 512,
                ]);

            if (! $response->successful()) {
                Log::warning('NotificationTemplateTranslateService: неуспешный ответ DeepSeek', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || $content === '') {
                Log::warning('NotificationTemplateTranslateService: пустой или некорректный ответ', [
                    'content' => $content,
                ]);

                return null;
            }

            $jsonString = trim($content);

            if (! str_starts_with($jsonString, '{')) {
                if (preg_match('/\{.*\}/s', $content, $matches) && isset($matches[0])) {
                    $jsonString = $matches[0];
                }
            }

            $data = json_decode($jsonString, true);

            if (! is_array($data)) {
                Log::warning('NotificationTemplateTranslateService: не удалось распарсить JSON', [
                    'content' => $content,
                ]);

                return null;
            }

            $resultRu = isset($data['ru']) && is_string($data['ru']) ? trim($data['ru']) : (string) ($bodyRu ?? '');
            $resultEn = isset($data['en']) && is_string($data['en']) ? trim($data['en']) : (string) ($bodyEn ?? '');
            $resultCn = isset($data['cn']) && is_string($data['cn']) ? trim($data['cn']) : (string) ($bodyCn ?? '');

            return [
                'body_ru' => $resultRu,
                'body_en' => $resultEn,
                'body_cn' => $resultCn,
            ];
        } catch (\Throwable $e) {
            Log::error('NotificationTemplateTranslateService: ошибка при обращении к DeepSeek', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
