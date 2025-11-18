<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\UiContent;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Отображение главной страницы.
     */
    public function index(): Response
    {
        $tenders = Tender::query()
            ->where('status', 'open')
            ->where('is_finished', false)
            ->select(
                'id',
                'title',
                'title_en',
                'title_cn',
                'description',
                'description_en',
                'description_cn',
                'status',
                'created_at',
                'valid_until'
            )
            ->withCount('items')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $overrides = UiContent::getOverridesFor(app()->getLocale(), 'home.');

        return Inertia::render('Home', [
            'tenders' => $tenders,
            'ui_overrides' => $overrides,
        ]);
    }
}
