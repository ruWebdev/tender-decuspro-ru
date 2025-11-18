<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AdminContentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Content/Index', [
            'stats' => [
                'pages' => 12,
                'articles' => 45,
                'news' => 28,
                'drafts' => 7,
            ],
        ]);
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
