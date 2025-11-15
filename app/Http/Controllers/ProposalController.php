<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Tender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProposalController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Proposals/Index');
    }

    public function indexCustomer(Tender $tender): Response
    {
        $this->authorize('viewProposals', $tender);

        $proposals = $tender->proposals()
            ->with(['user:id,name'])
            ->withCount('items')
            ->orderByDesc('submitted_at')
            ->get();

        return Inertia::render('Proposals/IndexCustomer', [
            'tender' => $tender,
            'proposals' => $proposals,
        ]);
    }

    public function viewCustomer(Proposal $proposal): Response
    {
        $proposal->load(['tender', 'user', 'items.tenderItem']);

        $this->authorize('viewProposals', $proposal->tender);

        return Inertia::render('Proposals/ViewCustomer', [
            'proposal' => $proposal,
        ]);
    }

    public function participate(Tender $tender): Response
    {
        $this->authorize('participate', $tender);

        $user = request()->user();

        $proposal = Proposal::firstOrCreate(
            [
                'tender_id' => $tender->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'draft',
            ],
        );

        $tender->load(['items', 'winnerProposal.items.tenderItem']);
        $proposal->load('items');

        $winnerSummary = null;
        $winner = $tender->winnerProposal;

        if ($winner) {
            $winner->loadMissing('items.tenderItem');

            $winnerItems = $winner->items->map(function ($item) {
                return [
                    'tender_item_id' => $item->tender_item_id,
                    'price' => (float) $item->price,
                ];
            })->values();

            $winnerTotal = $winner->items->sum(function ($item) {
                $price = (float) $item->price;
                $quantity = (float) ($item->tenderItem?->quantity ?? 0);

                return $price * $quantity;
            });

            $winnerSummary = [
                'id' => $winner->id,
                'total' => $winnerTotal,
                'items' => $winnerItems,
            ];
        }

        return Inertia::render('Proposals/Participate', [
            'tender' => $tender,
            'proposal' => $proposal,
            'winner' => $winnerSummary,
        ]);
    }

    public function store(Request $request, Tender $tender): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.tender_item_id' => ['required', 'uuid'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.comment' => ['nullable', 'string'],
        ]);

        $proposal = Proposal::firstOrCreate(
            [
                'tender_id' => $tender->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'draft',
            ],
        );

        $this->authorize('update', $proposal);

        DB::transaction(function () use ($validated, $proposal): void {
            $items = collect($validated['items'])
                ->values()
                ->map(function (array $item): array {
                    return [
                        'tender_item_id' => $item['tender_item_id'],
                        'price' => $item['price'],
                        'comment' => $item['comment'] ?? null,
                    ];
                })
                ->all();

            $proposal->items()->delete();
            $proposal->items()->createMany($items);

            $proposal->forceFill([
                'status' => 'submitted',
                'submitted_at' => now(),
            ])->save();
        });

        return redirect()->route('proposals.participate', ['tender' => $tender->id]);
    }

    public function update(Request $request, Proposal $proposal): RedirectResponse
    {
        $this->authorize('update', $proposal);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.tender_item_id' => ['required', 'uuid'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.comment' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $proposal): void {
            $items = collect($validated['items'])
                ->values()
                ->map(function (array $item): array {
                    return [
                        'tender_item_id' => $item['tender_item_id'],
                        'price' => $item['price'],
                        'comment' => $item['comment'] ?? null,
                    ];
                })
                ->all();

            $proposal->items()->delete();
            $proposal->items()->createMany($items);
        });

        return redirect()->route('proposals.participate', ['tender' => $proposal->tender_id]);
    }
}
