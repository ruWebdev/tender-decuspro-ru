<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierDocument;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSuppliersController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::role(User::ROLE_SUPPLIER)
            ->select('id', 'name', 'email', 'is_blocked', 'created_at')
            ->withCount([
                'proposals',
                'supplierDocuments as documents_pending_count' => function ($q) {
                    $q->where('status', 'pending');
                },
                'supplierDocuments as documents_approved_count' => function ($q) {
                    $q->where('status', 'approved');
                },
                'supplierDocuments as documents_rejected_count' => function ($q) {
                    $q->where('status', 'rejected');
                },
            ]);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('is_blocked')) {
            $isBlocked = $request->input('is_blocked') === '1';
            $query->where('is_blocked', $isBlocked);
        }

        if ($request->boolean('requires_moderation')) {
            $query->whereExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('supplier_documents')
                    ->whereColumn('supplier_documents.user_id', 'users.id')
                    ->where('supplier_documents.status', 'pending');
            });
        }

        $suppliers = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Users/Suppliers', [
            'suppliers' => $suppliers,
            'filters' => [
                'search' => $request->input('search', ''),
                'is_blocked' => $request->input('is_blocked', ''),
                'requires_moderation' => $request->boolean('requires_moderation'),
            ],
        ]);
    }

    public function show(User $user): Response
    {
        $user->load(['supplierProfile']);

        $documents = SupplierDocument::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get(['id', 'type', 'file_path', 'status', 'moderation_comment', 'created_at']);

        $recentProposals = \App\Models\Proposal::query()
            ->where('user_id', $user->id)
            ->with(['tender' => function ($q) {
                $q->select('id', 'title', 'status', 'valid_until', 'finished_at');
            }])
            ->orderByDesc('submitted_at')
            ->limit(20)
            ->get(['id', 'tender_id', 'status', 'submitted_at']);

        $wonTenders = \App\Models\Tender::query()
            ->whereNotNull('winner_proposal_id')
            ->whereHas('winnerProposal', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('id', 'title', 'status', 'finished_at')
            ->orderByDesc('finished_at')
            ->limit(20)
            ->get();

        return Inertia::render('Admin/Users/SupplierShow', [
            'user' => $user->only(['id', 'name', 'email', 'is_blocked', 'created_at']) + [
                'role_names' => $user->role_names,
            ],
            'profile' => $user->supplierProfile?->only(['company_name', 'contact_data']),
            'documents' => $documents,
            'recent_proposals' => $recentProposals,
            'won_tenders' => $wonTenders,
        ]);
    }

    public function documents(User $user): Response
    {
        $docs = SupplierDocument::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get(['id', 'type', 'file_path', 'status', 'created_at']);

        return Inertia::render('Admin/Users/SupplierDocuments', [
            'user' => $user->only(['id', 'name', 'email']),
            'documents' => $docs,
        ]);
    }

    public function approveDocument(SupplierDocument $document): RedirectResponse
    {
        $document->update([
            'status' => 'approved',
            'moderation_comment' => null,
        ]);

        return back();
    }

    public function rejectDocument(SupplierDocument $document, Request $request): RedirectResponse
    {
        $document->update([
            'status' => 'rejected',
            'moderation_comment' => $request->input('moderation_comment'),
        ]);

        return back();
    }

    public function logs(User $user): Response
    {
        $logs = UserActivityLog::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get(['id', 'action', 'ip', 'user_agent', 'created_at']);

        return Inertia::render('Admin/Users/SupplierLogs', [
            'user' => $user->only(['id', 'name', 'email']),
            'logs' => $logs,
        ]);
    }
}
