<?php

namespace App\Http\Controllers;

use App\Models\SupplierDocument;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CabinetController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $moderation = null;
        $documentsByType = [];

        if ($user && method_exists($user, 'isSupplier') && $user->isSupplier()) {
            $documents = SupplierDocument::query()
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->get(['id', 'type', 'file_path', 'status', 'moderation_comment', 'created_at']);

            $hasDocuments = $documents->isNotEmpty();
            $hasPending = $documents->where('status', 'pending')->isNotEmpty();
            $hasRejected = $documents->where('status', 'rejected')->isNotEmpty();
            $hasApproved = $documents->where('status', 'approved')->isNotEmpty();

            if (! $hasDocuments) {
                $statusKey = 'waiting_documents';
            } elseif ($hasRejected) {
                $statusKey = 'rejected';
            } elseif ($hasPending) {
                $statusKey = 'in_review';
            } elseif ($hasApproved) {
                $statusKey = 'approved';
            } else {
                $statusKey = 'unknown';
            }

            $reason = $documents
                ->where('status', 'rejected')
                ->pluck('moderation_comment')
                ->filter()
                ->unique()
                ->implode("\n");

            $moderation = [
                'status' => $statusKey,
                'reason' => $reason,
            ];

            $documentsByType = $documents
                ->groupBy('type')
                ->map(function ($items) {
                    $doc = $items->first();

                    return [
                        'id' => $doc->id,
                        'type' => $doc->type,
                        'file_path' => $doc->file_path,
                        'status' => $doc->status,
                        'moderation_comment' => $doc->moderation_comment,
                        'created_at' => $doc->created_at,
                    ];
                })->toArray();
        }

        return Inertia::render('Cabinet/Index', [
            'moderation' => $moderation,
            'supplier_documents' => $documentsByType,
        ]);
    }
}
