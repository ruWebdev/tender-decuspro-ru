<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\TranslateTenderJob;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\User;
use App\Services\SystemLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminTendersController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Tender::query()->with('customer:id,name,email');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('is_finished')) {
            $query->where('is_finished', $request->boolean('is_finished'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        $tenders = $query
            ->select('id', 'title', 'title_en', 'title_cn', 'status', 'valid_until', 'created_at', 'customer_id', 'is_finished')
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $customers = User::role(User::ROLE_CUSTOMER)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $statuses = [
            'open' => 'Открыт',
            'closed' => 'Закрыт',
            'review' => 'На рассмотрении',
        ];

        return Inertia::render('Admin/Tenders/Index', [
            'tenders' => $tenders,
            'filters' => $request->only(['status', 'is_finished', 'customer_id']),
            'customers' => $customers,
            'statuses' => $statuses,
        ]);
    }

    public function create(): Response
    {
        $customers = User::role(User::ROLE_CUSTOMER)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Tenders/Create', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'uuid', 'exists:users,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'hidden_comment' => ['nullable', 'string'],
            'valid_until' => ['required', 'date'],
            'valid_until_time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string', Rule::in(['open', 'closed', 'review'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.title' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string'],
        ]);

        $tender = null;

        DB::transaction(function () use ($validated, &$tender): void {
            $tender = Tender::create([
                'customer_id' => $validated['customer_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'hidden_comment' => $validated['hidden_comment'] ?? null,
                'valid_until' => $validated['valid_until'],
                'valid_until_time' => $validated['valid_until_time'] ?? null,
                'status' => $validated['status'],
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

        return redirect()->route('admin.tenders.index')
            ->with('success', 'Тендер успешно создан');
    }

    public function show(Tender $tender): Response
    {
        $tender->load(['customer:id,name,email', 'items']);

        return Inertia::render('Admin/Tenders/Show', [
            'tender' => $tender,
        ]);
    }

    public function edit(Tender $tender): Response
    {
        $tender->load(['customer:id,name,email', 'items']);

        $customers = User::role(User::ROLE_CUSTOMER)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Tenders/Edit', [
            'tender' => $tender,
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, Tender $tender): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'uuid', 'exists:users,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'hidden_comment' => ['nullable', 'string'],
            'valid_until' => ['required', 'date'],
            'valid_until_time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'string', Rule::in(['open', 'closed', 'review'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.title' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $tender): void {
            $tender->update([
                'customer_id' => $validated['customer_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'hidden_comment' => $validated['hidden_comment'] ?? null,
                'valid_until' => $validated['valid_until'],
                'valid_until_time' => $validated['valid_until_time'] ?? null,
                'status' => $validated['status'],
            ]);

            $tender->items()->delete();

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

        TranslateTenderJob::dispatch($tender);

        return redirect()->route('admin.tenders.index')
            ->with('success', 'Тендер успешно обновлен');
    }

    public function retender(Tender $tender, SystemLogService $log): RedirectResponse
    {
        $newTender = null;

        DB::transaction(function () use ($tender, &$newTender): void {
            $round = (int) ($tender->round_number ?? 1) + 1;

            $newTender = Tender::create([
                'customer_id' => $tender->customer_id,
                'parent_tender_id' => $tender->id,
                'title' => $tender->getRawOriginal('title'),
                'title_en' => $tender->getRawOriginal('title_en'),
                'title_cn' => $tender->getRawOriginal('title_cn'),
                'description' => $tender->getRawOriginal('description'),
                'description_en' => $tender->getRawOriginal('description_en'),
                'description_cn' => $tender->getRawOriginal('description_cn'),
                'hidden_comment' => $tender->hidden_comment,
                'valid_until' => now()->addDays(7),
                'status' => 'open',
                'round_number' => $round,
            ]);

            $items = $tender->items
                ->sortBy('position_index')
                ->values()
                ->map(function (TenderItem $item, int $index): array {
                    return [
                        'title' => $item->title,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'position_index' => $item->position_index ?? $index,
                    ];
                })
                ->all();

            if (! empty($items)) {
                $newTender->items()->createMany($items);
            }
        });

        if ($newTender) {
            TranslateTenderJob::dispatch($newTender);

            $log->business('tender_retender_created', 'Создан новый раунд тендера (переторжка)', [
                'source_tender_id' => $tender->id,
                'new_tender_id' => $newTender->id,
                'round' => $newTender->round_number,
            ]);

            return redirect()->route('admin.tenders.show', $newTender->id)
                ->with('success', 'Переторжка создана на основе выбранного тендера');
        }

        return redirect()->route('admin.tenders.show', $tender->id)
            ->with('error', 'Не удалось создать переторжку');
    }

    public function destroy(Tender $tender): RedirectResponse
    {
        $tender->delete();

        return redirect()->route('admin.tenders.index')
            ->with('success', 'Тендер удален');
    }
}
