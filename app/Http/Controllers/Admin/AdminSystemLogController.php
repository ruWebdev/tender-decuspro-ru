<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSystemLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = SystemLog::query();

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('code')) {
            $query->where('code', $request->input('code'));
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->input('to'));
        }

        $logs = $query
            ->orderByDesc('created_at')
            ->limit(200)
            ->get(['id', 'level', 'code', 'message', 'context', 'created_at']);

        $levels = [
            'error',
            'warning',
            'info',
            'business',
        ];

        return Inertia::render('Admin/SystemLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['level', 'code', 'from', 'to']),
            'levels' => $levels,
        ]);
    }
}
