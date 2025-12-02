<?php

namespace App\Http\Controllers;

use App\Services\WeChatService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Контроллер для обработки webhook-запросов от WeChat
 */
class WeChatWebhookController extends Controller
{
    public function __construct(
        private readonly WeChatService $weChatService
    ) {}

    /**
     * Обработка GET-запроса — верификация сервера WeChat
     */
    public function verify(Request $request): Response
    {
        $signature = $request->input('signature', '');
        $timestamp = $request->input('timestamp', '');
        $nonce = $request->input('nonce', '');
        $echostr = $request->input('echostr', '');

        if ($this->weChatService->verifyServer($signature, $timestamp, $nonce)) {
            return response($echostr);
        }

        Log::warning('WeChat: Неудачная верификация сервера', [
            'signature' => $signature,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
        ]);

        return response('Verification failed', 403);
    }

    /**
     * Обработка POST-запроса — входящие сообщения и события
     */
    public function handle(Request $request): Response
    {
        $signature = $request->input('signature', '');
        $timestamp = $request->input('timestamp', '');
        $nonce = $request->input('nonce', '');

        // Верификация подписи
        if (! $this->weChatService->verifyServer($signature, $timestamp, $nonce)) {
            Log::warning('WeChat: Неверная подпись входящего сообщения');

            return response('Invalid signature', 403);
        }

        // Парсинг XML
        $xmlContent = $request->getContent();
        $data = $this->parseXml($xmlContent);

        if (! $data) {
            Log::error('WeChat: Не удалось распарсить XML', [
                'content' => $xmlContent,
            ]);

            return response('success');
        }

        Log::info('WeChat: Получено сообщение', $data);

        $msgType = $data['MsgType'] ?? '';

        // Обработка событий (подписка/отписка)
        if ($msgType === 'event') {
            $this->weChatService->handleSubscribeEvent($data);

            return response('success');
        }

        // Обработка сообщений
        $this->weChatService->handleIncomingMessage($data);

        return response('success');
    }

    /**
     * Парсинг XML от WeChat
     */
    private function parseXml(string $xml): ?array
    {
        if (empty($xml)) {
            return null;
        }

        try {
            // Отключаем загрузку внешних сущностей для безопасности
            $previousValue = libxml_disable_entity_loader(true);
            $xmlObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            libxml_disable_entity_loader($previousValue);

            if ($xmlObj === false) {
                return null;
            }

            return json_decode(json_encode($xmlObj), true);
        } catch (\Exception $e) {
            Log::error('WeChat: Ошибка парсинга XML', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
