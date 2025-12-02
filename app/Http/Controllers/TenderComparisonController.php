<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\Tender;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenderComparisonController extends Controller
{
    /**
     * Таблица сравнения предложений по тендеру.
     */
    public function index(Tender $tender, Request $request): Response
    {
        $this->authorize('viewProposals', $tender);

        $tender->load('items');

        $proposals = $tender->proposals()
            ->where('status', 'submitted')
            ->with(['user:id,name', 'items'])
            ->get();

        $tenderItems = $tender->items->keyBy('id');

        $itemsData = [];
        foreach ($tenderItems as $item) {
            $itemsData[$item->id] = [
                'id' => $item->id,
                'name' => $item->title,
                'quantity' => (float) $item->quantity,
                'best_price' => null,
                'proposals' => [],
            ];
        }

        $proposalTotals = [];

        foreach ($proposals as $proposal) {
            $total = 0.0;

            foreach ($proposal->items as $proposalItem) {
                $tenderItem = $tenderItems->get($proposalItem->tender_item_id);

                if (! $tenderItem) {
                    continue;
                }

                $price = (float) $proposalItem->price;
                $quantity = (float) $tenderItem->quantity;
                $sum = $price * $quantity;
                $total += $sum;

                $itemEntry = &$itemsData[$tenderItem->id];

                if ($itemEntry['best_price'] === null || $price < $itemEntry['best_price']) {
                    $itemEntry['best_price'] = $price;
                }

                $itemEntry['proposals'][] = [
                    'supplier' => $proposal->user?->name,
                    'price' => $price,
                    'sum' => $sum,
                ];
            }

            $proposalTotals[$proposal->id] = $total;
        }

        $user = $request->user();
        $component = ($user && method_exists($user, 'isAdmin') && ($user->isAdmin() || $user->isModerator()))
            ? 'Admin/Tenders/Comparison'
            : 'Tenders/Comparison';

        return Inertia::render($component, [
            'tender' => [
                'id' => $tender->id,
                'title' => $tender->title,
            ],
            'comparison' => [
                'items' => array_values($itemsData),
                'proposal_totals' => $proposalTotals,
            ],
        ]);
    }

    /**
     * Отображение лучших цен для поставщика по каждому товару тендера.
     */
    public function bestPrices(Tender $tender, Request $request): Response
    {
        $user = $request->user();

        $tender->load('items');

        $itemIds = $tender->items->pluck('id')->all();

        // Лучшие цены среди отправленных предложений
        $bestPrices = ProposalItem::query()
            ->whereIn('tender_item_id', $itemIds)
            ->whereHas('proposal', function ($query) use ($tender) {
                $query->where('tender_id', $tender->id)
                    ->where('status', 'submitted');
            })
            ->selectRaw('tender_item_id, MIN(price) as best_price')
            ->groupBy('tender_item_id')
            ->pluck('best_price', 'tender_item_id');

        // Текущие цены поставщика (черновик или отправленное предложение)
        $myProposal = Proposal::query()
            ->where('tender_id', $tender->id)
            ->where('user_id', $user->id)
            ->with('items')
            ->first();

        $myPrices = collect($myProposal?->items ?? [])
            ->keyBy('tender_item_id')
            ->map(fn($item) => $item->price);

        $items = [];

        foreach ($tender->items as $item) {
            $best = $bestPrices[$item->id] ?? null;
            $mine = $myPrices[$item->id] ?? null;

            $difference = null;
            if ($best !== null && $mine !== null) {
                $delta = (float) $mine - (float) $best;
                $difference = sprintf('%+.2f', $delta);
            }

            $items[$item->id] = [
                'id' => $item->id,
                'name' => $item->title,
                'best_price' => $best !== null ? (float) $best : null,
                'my_price' => $mine !== null ? (float) $mine : null,
                'difference' => $difference,
            ];
        }

        return Inertia::render('Tenders/BestPrices', [
            'tender' => [
                'id' => $tender->id,
                'title' => $tender->title,
            ],
            'items' => $items,
        ]);
    }
}
