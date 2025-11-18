<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UiContent;
use Inertia\Inertia;
use Inertia\Response;

class AdminContentController extends Controller
{
    public function index(): Response
    {
        $items = UiContent::query()
            ->where('key', 'LIKE', 'home.%')
            ->orderBy('key')
            ->get(['key', 'value_ru', 'value_en', 'value_cn']);

        return Inertia::render('Admin/Content/Index', [
            'items' => $items,
        ]);
    }

    public function saveHome(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.key' => ['required', 'string', 'regex:/^home\./'],
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

        return back();
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
