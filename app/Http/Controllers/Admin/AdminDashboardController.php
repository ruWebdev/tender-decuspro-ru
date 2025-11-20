<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tender;
use App\Models\User;
use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\SupplierProfile;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $totalTenders = Tender::query()->count();
        $submittedProposalsCount = Proposal::query()->where('status', 'submitted')->count();

        $stats = [
            'total_users' => User::count(),
            'active_tenders' => Tender::query()->where('is_finished', false)->count(),
            'suppliers' => User::role(User::ROLE_SUPPLIER)->count(),
            'suppliers_new' => User::role(User::ROLE_SUPPLIER)
                ->where('created_at', '>=', $now->copy()->subDays(30))
                ->count(),
            'suppliers_active' => User::role(User::ROLE_SUPPLIER)
                ->where(function ($q) {
                    $q->whereNull('is_blocked')->orWhere('is_blocked', false);
                })
                ->count(),
            'suppliers_blocked' => User::role(User::ROLE_SUPPLIER)
                ->where('is_blocked', true)
                ->count(),
            'average_bids_per_tender' => $totalTenders > 0 ? round($submittedProposalsCount / $totalTenders, 1) : 0,
            'budget_savings' => 0.0, // нет полей ожидаемой цены, поэтому пока 0
        ];

        $recentTenders = Tender::query()
            ->select('id', 'title', 'status', 'created_at')
            ->latest()
            ->limit(5)
            ->get();

        // Закупочный объем за текущий месяц: сумма цен по позициям побеждающих заявок завершенных/закрытых тендеров за период
        $winnerIdsForMonth = Tender::query()
            ->whereNotNull('winner_proposal_id')
            ->whereBetween('finished_at', [$monthStart, $monthEnd])
            ->pluck('winner_proposal_id');

        $procurementVolume = 0.0;
        if ($winnerIdsForMonth->isNotEmpty()) {
            $procurementVolume = (float) ProposalItem::query()
                ->whereIn('proposal_id', $winnerIdsForMonth)
                ->sum('price');
        }

        // Топ поставщиков по количеству выигранных тендеров (за все время)
        $wins = Proposal::query()
            ->selectRaw('user_id, COUNT(*) as wins')
            ->whereIn('id', function ($q) {
                $q->select('winner_proposal_id')->from('tenders')->whereNotNull('winner_proposal_id');
            })
            ->groupBy('user_id')
            ->orderByDesc('wins')
            ->limit(5)
            ->get();

        $supplierIds = $wins->pluck('user_id')->all();
        $users = User::query()->whereIn('id', $supplierIds)->get(['id', 'name']);
        $profiles = SupplierProfile::query()->whereIn('user_id', $supplierIds)->get(['user_id', 'company_name']);

        $userById = $users->keyBy('id');
        $profileByUserId = $profiles->keyBy('user_id');

        $topSuppliers = $wins->map(function ($row) use ($userById, $profileByUserId) {
            $user = $userById->get($row->user_id);
            $profile = $profileByUserId->get($row->user_id);
            return [
                'name' => $profile?->company_name ?? ($user?->name ?? '—'),
                'won_tenders_count' => (int) $row->wins,
            ];
        });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentTenders' => $recentTenders,
            'procurement_volume' => $procurementVolume,
            'top_suppliers' => $topSuppliers,
            'locale' => app()->getLocale(),
        ]);
    }
}
