<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use App\Models\UiContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            ...parent::share($request),
            'auth' => fn() => [
                'user' => Auth::check() ? Auth::user()->loadMissing('roles') : null,
            ],
            'locale' => fn() => $locale,
            'translations' => fn() => [
                'nav' => Lang::get('nav'),
                'home' => Lang::get('home'),
                'tenders' => Lang::get('tenders'),
                'proposals' => Lang::get('proposals'),
                'auth' => Lang::get('auth'),
                'common' => Lang::get('common'),
                'admin' => Lang::get('admin'),
            ],
            'ui_overrides' => fn() => Cache::remember(
                "ui_translations_{$locale}",
                now()->addMinutes(5),
                fn() => UiContent::getAllTranslations($locale)
            ),
            'site_settings' => fn() => Cache::remember(
                'site_settings',
                now()->addMinutes(10),
                fn() => [
                    'site_name' => SystemSetting::getValue('site_name', 'QBS Tenders'),
                    'site_phone' => SystemSetting::getValue('site_phone', '+7 (000) 000-00-00'),
                    'site_email' => SystemSetting::getValue('site_email', 'info@tenderhub.com'),
                    'site_address' => SystemSetting::getValue('site_address', ''),
                    'stats_tenders' => SystemSetting::getValue('stats_tenders', '500+'),
                    'stats_vendors' => SystemSetting::getValue('stats_vendors', '1200+'),
                    'stats_total_value' => SystemSetting::getValue('stats_total_value', '$50M+'),
                    'stats_success_rate' => SystemSetting::getValue('stats_success_rate', '98%'),
                ]
            ),
        ];
    }
}
