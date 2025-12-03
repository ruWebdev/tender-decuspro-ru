<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class TranslatorService
{
    /**
     * Перевод текста на указанный язык через DeepSeek API.
     */
    public function translate(?string $text, string $lang): ?string
    {
        if ($text === null) {
            return null;
        }

        $text = trim($text);

        if ($text == '') {
            return null;
        }

        $langMap = [
            'ru' => 'Russian',
            'en' => 'English',
            'zh' => 'Chinese',
            'cn' => 'Chinese',
        ];

        $targetLang = $langMap[$lang] ?? 'English';

        $prompt = "Translate the following text to {$targetLang}. Return only the translated text without any comments or formatting.\n\n" . $text;

        try {
            $baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');
            $token = Setting::get('deepseek_api_key') ?? config('services.deepseek.token');

            if (! $token) {
                return null;
            }

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
                    'max_tokens' => 256,
                ]);

            if ($response->failed()) {
                return null;
            }

            $translated = $response->json('choices.0.message.content');

            if (! is_string($translated) || $translated === '') {
                return null;
            }

            return trim($translated);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
