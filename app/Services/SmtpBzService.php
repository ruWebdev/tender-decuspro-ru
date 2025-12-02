<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для отправки писем через SMTP.bz API
 */
class SmtpBzService
{
    private const API_URL = 'https://api.smtp.bz/v1/smtp/send';

    /**
     * Проверить, настроен ли API-ключ SMTP.bz
     */
    public function hasApiKey(): bool
    {
        return (bool) Setting::get('smtp_bz_api_key');
    }

    /**
     * Отправить письмо через SMTP.bz
     *
     * @return array{success: bool, status?: int, raw?: string, error?: string}
     */
    public function send(
        string $to,
        string $subject,
        string $html,
        ?string $text = null,
        ?string $from = null,
        ?string $fromName = null,
        ?string $replyTo = null
    ): array {
        $apiKey = Setting::get('smtp_bz_api_key');

        if (! $apiKey) {
            return [
                'success' => false,
                'error' => 'missing_api_key',
            ];
        }

        $fromAddress = $from ?: config('mail.from.address');
        $fromName = $fromName ?: config('mail.from.name');
        $replyTo = $replyTo ?: $fromAddress;

        try {
            $response = Http::withHeaders([
                'authorization' => $apiKey,
            ])->asForm()
                ->timeout(30)
                ->post(self::API_URL, [
                    'subject' => $subject,
                    'name' => $fromName,
                    'html' => $html,
                    'reply' => $replyTo,
                    'from' => $fromAddress,
                    'to' => $to,
                    'to_name' => null,
                    'headers' => null,
                    'text' => $text,
                ]);

            $raw = $response->body();

            if (! $response->successful()) {
                Log::error('SMTP.bz: ошибка отправки письма', [
                    'status' => $response->status(),
                    'body' => $raw,
                ]);

                return [
                    'success' => false,
                    'status' => $response->status(),
                    'raw' => $raw,
                    'error' => 'http_error',
                ];
            }

            return [
                'success' => true,
                'status' => $response->status(),
                'raw' => $raw,
            ];
        } catch (\Throwable $e) {
            Log::error('SMTP.bz: исключение при отправке письма', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
