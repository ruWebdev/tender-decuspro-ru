<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Tender;
use App\Notifications\WinnerSelected;
use App\Notifications\YouLostTender;
use App\Notifications\YouWonTender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class TenderFinishController extends Controller
{
    /**
     * Страница выбора победителя.
     */
    public function index(Tender $tender): Response
    {
        $this->authorize('finish', $tender);

        $proposals = $tender->proposals()
            ->where('status', 'submitted')
            ->with(['user:id,name', 'items'])
            ->get();

        return Inertia::render('Tenders/Finish', [
            'tender' => $tender,
            'proposals' => $proposals,
        ]);
    }

    /**
     * Выбор победителя и завершение тендера.
     */
    public function finish(Request $request, Tender $tender): RedirectResponse
    {
        $this->authorize('update', $tender);

        $data = $request->validate([
            'proposal_id' => ['required', 'uuid'],
        ]);

        $proposal = Proposal::query()
            ->where('id', $data['proposal_id'])
            ->where('tender_id', $tender->id)
            ->with(['items.tenderItem', 'user'])
            ->firstOrFail();

        // Обновляем статус тендера.
        $tender->winner_proposal_id = $proposal->id;
        $tender->is_finished = true;
        $tender->finished_at = now();
        $tender->save();

        // Подготовка данных по победителю.
        $winnerItems = [];
        $winnerTotal = 0.0;

        foreach ($proposal->items as $item) {
            $title = $item->tenderItem?->title ?? '';
            $quantity = (float) ($item->tenderItem?->quantity ?? 0);
            $price = (float) $item->price;
            $sum = $price * $quantity;

            $winnerItems[] = [
                'title' => $title,
                'quantity' => $quantity,
                'price' => $price,
                'sum' => $sum,
            ];

            $winnerTotal += $sum;
        }

        // Победитель.
        $winnerUser = $proposal->user;

        if ($winnerUser && $winnerUser->email) {
            Notification::send($winnerUser, new YouWonTender($tender, $winnerTotal, $winnerItems));
        }

        // Проигравшие.
        $loserProposals = Proposal::query()
            ->where('tender_id', $tender->id)
            ->where('status', 'submitted')
            ->where('id', '!=', $proposal->id)
            ->with('user')
            ->get();

        $bestItems = array_map(static function (array $item): array {
            return [
                'title' => $item['title'],
                'price' => $item['price'],
            ];
        }, $winnerItems);

        $loserUsers = $loserProposals
            ->pluck('user')
            ->filter(fn($user) => $user && $user->email)
            ->unique('id')
            ->values();

        if ($loserUsers->isNotEmpty()) {
            Notification::send($loserUsers, new YouLostTender($tender, $winnerTotal, $bestItems));
        }

        // Заказчик.
        $customer = $tender->customer;

        if ($customer && $customer->email) {
            $supplierName = $winnerUser?->name ?? 'Поставщик';
            Notification::send($customer, new WinnerSelected($tender, $supplierName, $winnerTotal));
        }

        return redirect()->route('proposals.index.customer', ['tender' => $tender->id]);
    }
}
