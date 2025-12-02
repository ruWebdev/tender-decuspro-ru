<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSupplier;
use App\Models\WeChatConversation;
use App\Models\WeChatMessage;
use App\Models\WeChatSettings;
use App\Services\TranslatorService;
use App\Services\WeChatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для управления WeChat в админ-панели
 */
class AdminWeChatController extends Controller
{
    public function __construct(
        private readonly WeChatService $weChatService,
        private readonly TranslatorService $translatorService
    ) {}

    /**
     * Главная страница WeChat — список диалогов
     */
    public function index(Request $request): Response
    {
        $query = WeChatConversation::query()
            ->with(['platformSupplier:id,name', 'user:id,name'])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('direction', 'incoming')->where('is_read', false);
            }]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nickname', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%")
                    ->orWhereHas('platformSupplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->boolean('unread_only')) {
            $query->whereHas('messages', function ($q) {
                $q->where('direction', 'incoming')->where('is_read', false);
            });
        }

        $conversations = $query
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        // Добавляем последнее сообщение к каждому диалогу
        $conversations->getCollection()->transform(function ($conversation) {
            $conversation->last_message = $conversation->messages()
                ->latest()
                ->first(['id', 'content', 'direction', 'created_at']);

            return $conversation;
        });

        $settings = WeChatSettings::first();
        $isConfigured = $this->weChatService->isConfigured();

        // Поставщики для привязки
        $suppliers = PlatformSupplier::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/WeChat/Index', [
            'conversations' => $conversations,
            'filters' => $request->only(['search', 'unread_only']),
            'settings' => $settings ? [
                'app_id' => $settings->app_id,
                'token' => $settings->token,
                'encoding_aes_key' => $settings->encoding_aes_key,
                'is_active' => $settings->is_active,
                'has_secret' => (bool) $settings->app_secret,
            ] : null,
            'isConfigured' => $isConfigured,
            'suppliers' => $suppliers,
            'webhookUrl' => route('wechat.webhook'),
        ]);
    }

    /**
     * Просмотр диалога с сообщениями
     */
    public function show(WeChatConversation $conversation): Response
    {
        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get();

        // Отмечаем входящие сообщения как прочитанные
        $conversation->messages()
            ->where('direction', 'incoming')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $suppliers = PlatformSupplier::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/WeChat/Show', [
            'conversation' => $conversation->load(['platformSupplier:id,name', 'user:id,name']),
            'messages' => $messages,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Отправить сообщение
     */
    public function sendMessage(Request $request, WeChatConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $message = $this->weChatService->sendTextMessage(
            $conversation,
            $data['content'],
            auth()->id()
        );

        if (! $message) {
            return back()->with('error', 'Не удалось отправить сообщение. Проверьте настройки WeChat.');
        }

        return back()->with('success', 'Сообщение отправлено');
    }

    /**
     * Перевести сообщение на русский
     */
    public function translateMessage(WeChatMessage $message): RedirectResponse
    {
        if ($message->translated_content_ru) {
            return back();
        }

        $translated = $this->translatorService->translate($message->content, 'ru');

        if ($translated) {
            $message->update(['translated_content_ru' => $translated]);
        }

        return back();
    }

    /**
     * Привязать диалог к поставщику
     */
    public function linkSupplier(Request $request, WeChatConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'platform_supplier_id' => ['nullable', 'uuid', 'exists:platform_suppliers,id'],
        ]);

        $conversation->update([
            'platform_supplier_id' => $data['platform_supplier_id'],
        ]);

        return back()->with('success', 'Поставщик привязан');
    }

    /**
     * Обновить заметку к диалогу
     */
    public function updateRemark(Request $request, WeChatConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'remark' => ['nullable', 'string', 'max:255'],
        ]);

        $conversation->update([
            'remark' => $data['remark'],
        ]);

        return back()->with('success', 'Заметка обновлена');
    }

    /**
     * Сохранить настройки WeChat
     */
    public function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_id' => ['nullable', 'string', 'max:100'],
            'app_secret' => ['nullable', 'string', 'max:100'],
            'token' => ['nullable', 'string', 'max:100'],
            'encoding_aes_key' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $this->weChatService->saveSettings($data);

        return back()->with('success', 'Настройки WeChat сохранены');
    }

    /**
     * Проверить подключение к WeChat API
     */
    public function testConnection(): RedirectResponse
    {
        $token = $this->weChatService->refreshAccessToken();

        if ($token) {
            return back()->with('success', 'Подключение успешно! Access token получен.');
        }

        return back()->with('error', 'Не удалось подключиться к WeChat API. Проверьте AppID и AppSecret.');
    }

    /**
     * Удалить диалог
     */
    public function destroy(WeChatConversation $conversation): RedirectResponse
    {
        $conversation->delete();

        return redirect()->route('admin.wechat.index')
            ->with('success', 'Диалог удалён');
    }
}
