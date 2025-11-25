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
        $locale = $request->session()->get('locale');

        // Если язык явно не выбран в сессии, задаем значение по умолчанию
        if (! $locale) {
            $user = $request->user();

            // Для администратора и модератора по умолчанию оставляем русский
            if (
                $user && method_exists($user, 'isAdmin') && method_exists($user, 'isModerator')
                && ($user->isAdmin() || $user->isModerator())
            ) {
                $locale = 'ru';
            } else {
                // Для гостей и обычных пользователей по умолчанию используем китайский
                $locale = 'cn';
            }
        }

        // Ограничиваем список поддерживаемых локалей.
        if (! in_array($locale, ['ru', 'en', 'cn'], true)) {
            $locale = 'ru';
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
