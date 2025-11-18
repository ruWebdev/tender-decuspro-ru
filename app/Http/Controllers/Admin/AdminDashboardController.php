<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $stats = [
            'total_users' => User::count(),
            'active_tenders' => Tender::query()->where('is_finished', false)->count(),
            'suppliers' => User::query()->where('role', User::ROLE_SUPPLIER)->count(),
        ];

        $recentTenders = Tender::query()
            ->select('id', 'title', 'status', 'created_at')
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentTenders' => $recentTenders,
        ]);
    }
}
