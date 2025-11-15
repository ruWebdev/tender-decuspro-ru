<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\JsonResponse;

class ProposalTotalController extends Controller
{
    public function show(Proposal $proposal): JsonResponse
    {
        $this->authorize('viewProposals', $proposal->tender);

        $proposal->loadMissing('items.tenderItem');

        $total = $proposal->items->sum(function ($item) {
            $price = (float) $item->price;
            $quantity = (float) ($item->tenderItem?->quantity ?? 0);

            return $price * $quantity;
        });

        return response()->json([
            'total' => $total,
        ]);
    }
}
