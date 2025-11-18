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
        $slugs = ['user-agreement', 'privacy-policy', 'procurement-rules'];
        $pages = ContentPage::whereIn('slug', $slugs)->get()->keyBy('slug');

        // Ensure pages exist
        foreach ($slugs as $slug) {
            if (!isset($pages[$slug])) {
                $pages[$slug] = ContentPage::create([
                    'slug' => $slug,
                    'published' => true,
                ]);
            }
        }

        return Inertia::render('Admin/Content/StaticPages', [
            'pages' => [
                'user_agreement' => [
                    'slug' => 'user-agreement',
                    'title_ru' => $pages['user-agreement']->title_ru,
                    'title_en' => $pages['user-agreement']->title_en,
                    'title_cn' => $pages['user-agreement']->title_cn,
                    'body_ru' => $pages['user-agreement']->body_ru,
                    'body_en' => $pages['user-agreement']->body_en,
                    'body_cn' => $pages['user-agreement']->body_cn,
                    'published' => $pages['user-agreement']->published,
                ],
                'privacy_policy' => [
                    'slug' => 'privacy-policy',
                    'title_ru' => $pages['privacy-policy']->title_ru,
                    'title_en' => $pages['privacy-policy']->title_en,
                    'title_cn' => $pages['privacy-policy']->title_cn,
                    'body_ru' => $pages['privacy-policy']->body_ru,
                    'body_en' => $pages['privacy-policy']->body_en,
                    'body_cn' => $pages['privacy-policy']->body_cn,
                    'published' => $pages['privacy-policy']->published,
                ],
                'procurement_rules' => [
                    'slug' => 'procurement-rules',
                    'title_ru' => $pages['procurement-rules']->title_ru,
                    'title_en' => $pages['procurement-rules']->title_en,
                    'title_cn' => $pages['procurement-rules']->title_cn,
                    'body_ru' => $pages['procurement-rules']->body_ru,
                    'body_en' => $pages['procurement-rules']->body_en,
                    'body_cn' => $pages['procurement-rules']->body_cn,
                    'published' => $pages['procurement-rules']->published,
                ],
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pages' => ['required', 'array'],
            'pages.user_agreement' => ['required', 'array'],
            'pages.privacy_policy' => ['required', 'array'],
            'pages.procurement_rules' => ['required', 'array'],

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
