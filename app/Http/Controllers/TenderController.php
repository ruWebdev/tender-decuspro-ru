<?php

namespace App\Http\Controllers;

use App\Jobs\TranslateTenderJob;
use App\Models\Tender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TenderController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Для заказчика — список его тендеров (массив)
        if ($user && method_exists($user, 'isCustomer') && $user->isCustomer()) {
            $tenders = Tender::query()
                ->where('customer_id', $user->id)
                ->select('id', 'title', 'title_en', 'title_cn', 'status', 'valid_until', 'created_at')
                ->withCount(['items', 'proposals'])
                ->orderByDesc('created_at')
                ->get();

            return Inertia::render('Tenders/Index', [
                'tenders' => $tenders,
            ]);
        }

        // Для поставщика — все активные тендеры (пагинация) + его заявка (если есть)
        $tenders = Tender::query()
            ->where('status', 'open')
            ->where(function ($q) {
                $q->whereNull('is_finished')->orWhere('is_finished', false);
            })
            ->select('id', 'title', 'title_en', 'title_cn', 'status', 'valid_until', 'created_at')
            ->withCount('items')
            ->selectSub(
                DB::table('proposals')
                    ->select('id')
                    ->whereColumn('proposals.tender_id', 'tenders.id')
                    ->where('proposals.user_id', $user->id)
                    ->limit(1),
                'my_proposal_id'
            )
            ->selectSub(
                DB::table('proposals')
                    ->select('status')
                    ->whereColumn('proposals.tender_id', 'tenders.id')
                    ->where('proposals.user_id', $user->id)
                    ->limit(1),
                'my_proposal_status'
            )
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tenders/Index', [
            'tenders' => $tenders,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Tender::class);

        return Inertia::render('Tenders/Create');
    }

    public function show(Tender $tender): Response
    {
        $tender->load('items');

        $publicQuestions = $tender->questions()
            ->where('is_public', true)
            ->with(['user:id,name'])
            ->orderBy('created_at')
            ->get(['id', 'tender_id', 'user_id', 'question', 'answer', 'created_at']);

        $user = request()->user();
        $myProposal = null;
        if ($user && method_exists($user, 'isSupplier') && $user->isSupplier()) {
            $p = $tender->proposals()
                ->where('user_id', $user->id)
                ->select('id', 'status')
                ->first();
            if ($p) {
                $myProposal = [
                    'id' => $p->id,
                    'status' => $p->status,
                ];
            }
        }

        return Inertia::render('Tenders/Show', [
            'tender' => $tender,
            'questions' => $publicQuestions,
            'my_proposal' => $myProposal,
        ]);
    }



    public function store(Request $request): RedirectResponse
    {
        $this->authorize('store', Tender::class);

        $user = $request->user();

        $validated = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'hidden_comment' => ['nullable', 'string'],
            'valid_until' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.title' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string'],
        ]);

        $tender = null;

        DB::transaction(function () use ($validated, $user, &$tender): void {
            $tender = Tender::create([
                'customer_id' => $user->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'hidden_comment' => $validated['hidden_comment'] ?? null,
                'valid_until' => $validated['valid_until'],
            ]);

            $items = collect($validated['items'])
                ->values()
                ->map(function (array $item, int $index): array {
                    return [
                        'title' => $item['title'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'] ?? null,
                        'position_index' => $index,
                    ];
                })
                ->all();

            $tender->items()->createMany($items);
        });

        if ($tender) {
            TranslateTenderJob::dispatch($tender);
        }

        return redirect()->route('tenders.index');
    }
}
