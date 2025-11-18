<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContentPageController extends Controller
{
    public function show(Request $request, string $slug): Response
    {
        $page = ContentPage::where('slug', $slug)->where('published', true)->firstOrFail();

        $locale = app()->getLocale();

        return Inertia::render('Docs/Show', [
            'slug' => $slug,
            'title' => $page->getTitleFor($locale) ?? '',
            'body' => $page->getBodyFor($locale) ?? '',
        ]);
    }
}
