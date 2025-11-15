<?php

namespace App\Services;

use App\Models\Tender;
use App\Models\TenderItem;

class ProposalAnalysisService
{
    /**
     * Возвращает минимальную цену среди всех предложений по позиции тендера.
     */
    public function getBestPriceForItem(TenderItem $item): ?float
    {
        $price = $item->proposalItems()
            ->whereHas('proposal', function ($query) {
                $query->where('status', 'submitted');
            })
            ->min('price');

        return $price !== null ? (float) $price : null;
    }

    /**
     * Формирует структуру сравнения предложений для тендера.
     *
     * @return array{
     *     items: list<array{id:string,name:string,quantity:float,best_price:float|null,proposals:list<array{supplier:?string,price:float,sum:float}>>>,
     *     proposal_totals: array<string,float>,
     * }
     */
    public function buildComparison(Tender $tender): array
    {
        $tender->loadMissing('items');

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
                    'proposal_id' => $proposal->id,
                    'supplier' => $proposal->user?->name,
                    'price' => $price,
                    'sum' => $sum,
                ];
            }

            $proposalTotals[$proposal->id] = $total;
        }

        return [
            'items' => array_values($itemsData),
            'proposal_totals' => $proposalTotals,
        ];
    }
}
