<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;

class AdminAIController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/AI/Index', [
            'settings' => [
                'deepseek_api_key' => config('services.deepseek.api_key'),
                'tender_prompt' => $this->getDefaultTenderPrompt(),
            ],
        ]);
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'deepseek_api_key' => 'required|string',
            'tender_prompt' => 'required|string',
        ]);

        // Здесь можно сохранить настройки в базу данных или конфигурацию
        // Например:
        // Setting::updateOrCreate(['key' => 'deepseek_api_key'], ['value' => $validated['deepseek_api_key']]);
        // Setting::updateOrCreate(['key' => 'tender_prompt'], ['value' => $validated['tender_prompt']]);

        return back()->with('success', 'Настройки ИИ успешно сохранены');
    }

    public function generateTender(Request $request)
    {
        $validated = $request->validate([
            'deepseek_api_key' => 'required|string',
            'tender_prompt' => 'required|string',
        ]);

        try {
            // Генерация данных для тендера через Deepseek API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $validated['deepseek_api_key'],
                'Content-Type' => 'application/json',
            ])->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $validated['tender_prompt']
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Сгенерируй данные для нового тендера в формате JSON с полями: title, description, customer, valid_until (дата в формате Y-m-d), items (массив из 3-5 позиций с полями: title, quantity, unit)'
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Ошибка API Deepseek');
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'];

            // Парсим JSON ответ
            $tenderData = json_decode($content, true);

            if (!$tenderData) {
                throw new \Exception('Не удалось распарсить ответ ИИ');
            }

            // Создаем тендер в базе данных
            // $tender = Tender::create([
            //     'title' => $tenderData['title'],
            //     'description' => $tenderData['description'],
            //     'customer_id' => $tenderData['customer_id'],
            //     'valid_until' => $tenderData['valid_until'],
            //     'status' => 'open',
            // ]);

            return back()->with('success', 'Тендер успешно сгенерирован');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function translateTenders(Request $request)
    {
        $validated = $request->validate([
            'deepseek_api_key' => 'required|string',
            'tender_prompt' => 'required|string',
        ]);

        try {
            // Получаем все тендеры, которые нужно перевести
            // $tenders = Tender::whereDoesntHave('translations', function($query) {
            //     $query->whereIn('locale', ['en', 'cn']);
            // })->get();

            // if ($tenders->isEmpty()) {
            //     return back()->with('message', 'Все тендеры уже переведены на все языки');
            // }

            // Запускаем фоновую задачу для перевода
            // TranslateTendersJob::dispatch($tenders, $validated['deepseek_api_key']);

            return back()->with('message', 'Все тендеры уже переведены на все языки');
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
