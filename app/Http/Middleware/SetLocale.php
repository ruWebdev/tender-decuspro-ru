<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Обработка входящего запроса и установка локали приложения.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('locale', 'ru');

        // Ограничиваем список поддерживаемых локалей.
        if (! in_array($locale, ['ru', 'en', 'cn'], true)) {
            $locale = 'ru';
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
