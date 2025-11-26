<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

/**
 * Контроллер для скрытой страницы блокировки проекта
 */
class SystemBlockController extends Controller
{
    /**
     * Показать страницу блокировки
     */
    public function index()
    {
        $isBlocked = SystemSetting::isProjectBlocked();

        return view('system.block', [
            'isBlocked' => $isBlocked,
        ]);
    }

    /**
     * Переключить статус блокировки
     */
    public function toggle(Request $request)
    {
        $isBlocked = SystemSetting::isProjectBlocked();
        SystemSetting::setProjectBlocked(!$isBlocked);

        return redirect()->back()->with('success', !$isBlocked ? 'Проект заблокирован' : 'Проект разблокирован');
    }
}
