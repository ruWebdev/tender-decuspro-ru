<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\TranslateTendersJob;
use App\Models\Setting;
use App\Services\TenderGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class AdminAIController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/AI/Index', [
            'settings' => [
                'deepseek_api_key' => Setting::get('deepseek_api_key') ?? config('services.deepseek.token'),
                'tender_prompt' => Setting::get('tender_prompt') ?? $this->getDefaultTenderPrompt(),
            ],
        ]);
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'deepseek_api_key' => 'required|string',
            'tender_prompt' => 'required|string',
        ]);

        Setting::set('deepseek_api_key', $validated['deepseek_api_key']);
        Setting::set('tender_prompt', $validated['tender_prompt']);

        return back()->with('success', 'Настройки ИИ успешно сохранены');
    }

    public function generateTender(Request $request)
    {
        try {
            /** @var TenderGeneratorService $service */
            $service = app(TenderGeneratorService::class);
            $tender = $service->generate();

            if (! $tender) {
                return back()->withErrors(['error' => 'Не удалось сгенерировать тендер']);
            }

            return back()->with('success', 'Тендер успешно сгенерирован');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function translateTenders(Request $request)
    {
        $apiKey = Setting::get('deepseek_api_key') ?? config('services.deepseek.token');

        try {
            if (! $apiKey) {
                return back()->withErrors(['error' => 'API-ключ DeepSeek не настроен. Укажите ключ на странице настроек ИИ.']);
            }

            TranslateTendersJob::dispatch();

            return back()->with('message', 'Задача перевода тендеров поставлена в очередь. Перевод будет выполнен в фоне.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function getDefaultTenderPrompt(): string
    {
        return 'Ты — профессиональный помощник по созданию тендеров. Твоя задача — создать подробное и структурированное описание тендера на основе предоставленной информации.

Правила:
1. Используй профессиональный и ясный язык
2. Структурируй информацию по разделам:
   - Общее описание
   - Требования к исполнителю
   - Сроки выполнения
   - Критерии оценки
   - Условия оплаты
3. Добавь технические детали если они важны
4. Укажи ожидаемые результаты
5. Используй форматирование для лучшей читаемости

Создай описание тендера на основе следующих данных:
Название: {title}
Описание: {description}
Заказчик: {customer}
Сроки: {valid_until}

Сделай описание привлекательным для качественных исполнителей.';
    }
}
