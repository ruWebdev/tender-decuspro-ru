<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminStaticPagesController extends Controller
{
    public function edit(Request $request): Response
    {
        $slug = 'supplier-terms';
        $page = ContentPage::firstOrCreate(
            ['slug' => $slug],
            ['published' => true],
        );

        return Inertia::render('Admin/Content/StaticPages', [
            'pages' => [
                'supplier_terms' => [
                    'slug' => $slug,
                    'title_ru' => $page->title_ru,
                    'title_en' => $page->title_en,
                    'title_cn' => $page->title_cn,
                    'body_ru' => $page->body_ru,
                    'body_en' => $page->body_en,
                    'body_cn' => $page->body_cn,
                    'published' => $page->published,
                ],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pages' => ['required', 'array'],
            'pages.supplier_terms' => ['required', 'array'],

            'pages.*.slug' => ['required', 'string'],
            'pages.*.title_ru' => ['nullable', 'string'],
            'pages.*.title_en' => ['nullable', 'string'],
            'pages.*.title_cn' => ['nullable', 'string'],
            'pages.*.body_ru' => ['nullable', 'string'],
            'pages.*.body_en' => ['nullable', 'string'],
            'pages.*.body_cn' => ['nullable', 'string'],
            'pages.*.published' => ['required', 'boolean'],
        ]);

        foreach ($data['pages'] as $key => $payload) {
            $page = ContentPage::firstOrCreate(['slug' => $payload['slug']], ['published' => true]);
            $page->fill([
                'title_ru' => $payload['title_ru'] ?? null,
                'title_en' => $payload['title_en'] ?? null,
                'title_cn' => $payload['title_cn'] ?? null,
                'body_ru' => $payload['body_ru'] ?? null,
                'body_en' => $payload['body_en'] ?? null,
                'body_cn' => $payload['body_cn'] ?? null,
                'published' => (bool) ($payload['published'] ?? true),
            ])->save();
        }

        return back();
    }
}
