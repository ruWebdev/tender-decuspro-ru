<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminApplicationsController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Proposal::query()
            ->with([
                'user:id,name,email,is_blocked',
                'user.supplierProfile:id,user_id,company_name,contact_data',
                'tender:id,title,status,is_finished',
            ])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $proposals = $query
            ->select('id', 'tender_id', 'user_id', 'status', 'created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Applications/Index', [
            'proposals' => $proposals,
            'filters' => $request->only(['status']),
        ]);
    }
}
