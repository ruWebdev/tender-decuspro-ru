<?php

namespace App\Services;

use App\Models\WeChatConversation;
use App\Models\WeChatMessage;
use App\Models\WeChatSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для работы с WeChat Official Account API
 */
class WeChatService
{
    private const API_BASE_URL = 'https://api.weixin.qq.com/cgi-bin';

    private ?WeChatSettings $settings = null;

    /**
     * Получить настройки WeChat
     */
    public function getSettings(): ?WeChatSettings
    {
        if (! $this->settings) {
            $this->settings = WeChatSettings::getActive();
        }

        return $this->settings;
    }

    /**
     * Проверить, настроен ли WeChat
     */
    public function isConfigured(): bool
    {
        $settings = $this->getSettings();

        return $settings
            && $settings->app_id
            && $settings->app_secret
            && $settings->is_active;
    }

    /**
     * Получить access_token (с автообновлением)
     */
    public function getAccessToken(): ?string
    {
        $settings = $this->getSettings();

        if (! $settings) {
            return null;
        }

        if (! $settings->isAccessTokenExpired()) {
            return $settings->access_token;
        }

        return $this->refreshAccessToken();
    }

    /**
     * Обновить access_token
     */
    public function refreshAccessToken(): ?string
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->app_id || ! $settings->app_secret) {
            return null;
        }

        try {
            $response = Http::get(self::API_BASE_URL . '/token', [
                'grant_type' => 'client_credential',
                'appid' => $settings->app_id,
                'secret' => $settings->app_secret,
            ]);

            $data = $response->json();

            if (isset($data['access_token'])) {
                $settings->update([
                    'access_token' => $data['access_token'],
                    'access_token_expires_at' => now()->addSeconds($data['expires_in'] - 300),
                ]);

                return $data['access_token'];
            }

            Log::error('WeChat: Ошибка получения access_token', $data);

            return null;
        } catch (\Exception $e) {
            Log::error('WeChat: Исключение при получении access_token', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Отправить текстовое сообщение пользователю
     */
    public function sendTextMessage(WeChatConversation $conversation, string $content, ?int $senderId = null): ?WeChatMessage
    {
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            Log::error('WeChat: Не удалось получить access_token для отправки сообщения');

            return null;
        }

        try {
            $response = Http::post(self::API_BASE_URL . '/message/custom/send', [
                'access_token' => $accessToken,
            ], [
                'touser' => $conversation->wechat_openid,
                'msgtype' => 'text',
                'text' => [
                    'content' => $content,
                ],
            ]);

            $data = $response->json();

            if (isset($data['errcode']) && $data['errcode'] !== 0) {
                Log::error('WeChat: Ошибка отправки сообщения', $data);

                return null;
            }

            // Сохраняем сообщение в БД
            return WeChatMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => 'outgoing',
                'msg_type' => 'text',
                'content' => $content,
                'is_read' => true,
                'sender_user_id' => $senderId,
            ]);
        } catch (\Exception $e) {
            Log::error('WeChat: Исключение при отправке сообщения', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Обработать входящее сообщение от WeChat
     */
    public function handleIncomingMessage(array $data): ?WeChatMessage
    {
        $openId = $data['FromUserName'] ?? null;
        $msgType = $data['MsgType'] ?? 'text';
        $msgId = $data['MsgId'] ?? null;

        if (! $openId) {
            return null;
        }

        // Найти или создать диалог
        $conversation = WeChatConversation::firstOrCreate(
            ['wechat_openid' => $openId],
            [
                'is_subscribed' => true,
                'subscribed_at' => now(),
            ]
        );

        // Проверить, не дубликат ли сообщения
        if ($msgId) {
            $existing = WeChatMessage::where('wechat_msg_id', $msgId)->first();
            if ($existing) {
                return $existing;
            }
        }

        $content = $this->extractContent($data, $msgType);

        return WeChatMessage::create([
            'conversation_id' => $conversation->id,
            'direction' => 'incoming',
            'msg_type' => $msgType,
            'content' => $content,
            'raw_data' => $data,
            'wechat_msg_id' => $msgId,
            'is_read' => false,
        ]);
    }

    /**
     * Обработать событие подписки/отписки
     */
    public function handleSubscribeEvent(array $data): void
    {
        $openId = $data['FromUserName'] ?? null;
        $event = $data['Event'] ?? null;

        if (! $openId) {
            return;
        }

        $conversation = WeChatConversation::firstOrCreate(
            ['wechat_openid' => $openId],
            ['is_subscribed' => true]
        );

        if ($event === 'subscribe') {
            $conversation->update([
                'is_subscribed' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]);

            // Получить информацию о пользователе
            $this->updateUserInfo($conversation);
        } elseif ($event === 'unsubscribe') {
            $conversation->update([
                'is_subscribed' => false,
                'unsubscribed_at' => now(),
            ]);
        }
    }

    /**
     * Обновить информацию о пользователе WeChat
     */
    public function updateUserInfo(WeChatConversation $conversation): bool
    {
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return false;
        }

        try {
            $response = Http::get(self::API_BASE_URL . '/user/info', [
                'access_token' => $accessToken,
                'openid' => $conversation->wechat_openid,
                'lang' => 'zh_CN',
            ]);

            $data = $response->json();

            if (isset($data['errcode'])) {
                Log::error('WeChat: Ошибка получения информации о пользователе', $data);

                return false;
            }

            $conversation->update([
                'nickname' => $data['nickname'] ?? null,
                'avatar_url' => $data['headimgurl'] ?? null,
                'wechat_unionid' => $data['unionid'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('WeChat: Исключение при получении информации о пользователе', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Верификация сервера WeChat
     */
    public function verifyServer(string $signature, string $timestamp, string $nonce): bool
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->token) {
            return false;
        }

        $tmpArr = [$settings->token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode('', $tmpArr);
        $tmpStr = sha1($tmpStr);

        return $tmpStr === $signature;
    }

    /**
     * Извлечь контент из сообщения в зависимости от типа
     */
    private function extractContent(array $data, string $msgType): ?string
    {
        return match ($msgType) {
            'text' => $data['Content'] ?? null,
            'image' => '[Изображение]',
            'voice' => $data['Recognition'] ?? '[Голосовое сообщение]',
            'video' => '[Видео]',
            'location' => sprintf(
                '[Геолокация: %s, %s]',
                $data['Location_X'] ?? '',
                $data['Location_Y'] ?? ''
            ),
            'link' => $data['Title'] ?? '[Ссылка]',
            default => null,
        };
    }

    /**
     * Сохранить настройки WeChat
     */
    public function saveSettings(array $data): WeChatSettings
    {
        $settings = WeChatSettings::first();

        if (! $settings) {
            $settings = new WeChatSettings();
        }

        $settings->fill([
            'app_id' => $data['app_id'] ?? null,
            'token' => $data['token'] ?? null,
            'encoding_aes_key' => $data['encoding_aes_key'] ?? null,
            'is_active' => $data['is_active'] ?? false,
        ]);

        // Обновляем app_secret только если он передан
        if (! empty($data['app_secret'])) {
            $settings->app_secret = $data['app_secret'];
        }

        $settings->save();

        // Сбрасываем кеш настроек
        $this->settings = null;

        return $settings;
    }
}
