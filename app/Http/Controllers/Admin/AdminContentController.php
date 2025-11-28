<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\UiContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class AdminContentController extends Controller
{
    public function index(): Response
    {
        $items = UiContent::query()
            ->orderBy('key')
            ->get(['key', 'value_ru', 'value_en', 'value_cn']);

        return Inertia::render('Admin/Content/Index', [
            'items' => $items,
        ]);
    }

    public function saveHome(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.key' => ['required', 'string'],
            'items.*.value_ru' => ['nullable', 'string'],
            'items.*.value_en' => ['nullable', 'string'],
            'items.*.value_cn' => ['nullable', 'string'],
        ]);

        foreach ($data['items'] as $row) {
            UiContent::updateOrCreate(
                ['key' => $row['key']],
                [
                    'value_ru' => $row['value_ru'] ?? null,
                    'value_en' => $row['value_en'] ?? null,
                    'value_cn' => $row['value_cn'] ?? null,
                ]
            );
        }

        // Очистить кеш переводов
        Cache::forget('ui_translations_ru');
        Cache::forget('ui_translations_en');
        Cache::forget('ui_translations_cn');

        return back()->with('success', 'Контент сохранен');
    }

    /**
     * Страница настроек сайта
     */
    public function siteSettings(): Response
    {
        $settings = [
            'site_name' => SystemSetting::getValue('site_name', 'QBS Tenders'),
            'site_phone' => SystemSetting::getValue('site_phone', '+7 (000) 000-00-00'),
            'site_email' => SystemSetting::getValue('site_email', 'info@tenderhub.com'),
            'site_address' => SystemSetting::getValue('site_address', ''),
            'stats_tenders' => SystemSetting::getValue('stats_tenders', '500+'),
            'stats_vendors' => SystemSetting::getValue('stats_vendors', '1200+'),
            'stats_total_value' => SystemSetting::getValue('stats_total_value', '$50M+'),
            'stats_success_rate' => SystemSetting::getValue('stats_success_rate', '98%'),
        ];

        return Inertia::render('Admin/Content/SiteSettings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Сохранение настроек сайта
     */
    public function saveSiteSettings(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_email' => ['nullable', 'string', 'email', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:500'],
            'stats_tenders' => ['nullable', 'string', 'max:50'],
            'stats_vendors' => ['nullable', 'string', 'max:50'],
            'stats_total_value' => ['nullable', 'string', 'max:50'],
            'stats_success_rate' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::setValue($key, $value ?? '');
        }

        // Очистить кеш
        Cache::forget('site_settings');

        return back()->with('success', 'Настройки сохранены');
    }

    public function pages(): Response
    {
        return Inertia::render('Admin/Content/Pages', [
            'pages' => [],
        ]);
    }

    public function articles(): Response
    {
        return Inertia::render('Admin/Content/Articles', [
            'articles' => [],
        ]);
    }

    public function news(): Response
    {
        return Inertia::render('Admin/Content/News', [
            'news' => [],
        ]);
    }
}
