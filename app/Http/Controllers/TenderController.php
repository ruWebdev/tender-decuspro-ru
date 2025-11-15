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

        $tenders = Tender::query()
            ->where('customer_id', $user->id)
            ->select('id', 'title', 'status', 'valid_until', 'created_at')
            ->withCount('items')
            ->orderByDesc('created_at')
            ->get();

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

        return Inertia::render('Tenders/Show', [
            'tender' => $tender,
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
