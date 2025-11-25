<?php

namespace App\Jobs;

use App\Models\Tender;
use App\Models\TenderItem;
use App\Services\SystemLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AutoRetenderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SystemLogService $log): void
    {
        $now = now();

        $tenders = Tender::query()
            ->where('auto_rebid', true)
            ->where('status', 'open')
            ->where(function ($query) {
                $query->whereNull('is_finished')->orWhere('is_finished', false);
            })
            ->where('valid_until', '<=', $now)
            ->get();

        foreach ($tenders as $tender) {
            if (! $tender->valid_until) {
                continue;
            }

            $deadline = $tender->valid_until->copy();

            if ($tender->valid_until_time && preg_match('/^(\d{2}):(\d{2})$/', $tender->valid_until_time, $m)) {
                $deadline->setTime((int) $m[1], (int) $m[2]);
            }

            if ($deadline->isFuture()) {
                continue;
            }

            DB::transaction(function () use ($tender, $log, $now): void {
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
                    'valid_until' => $now->copy()->addDays(7),
                    'status' => 'open',
                    'round_number' => $round,
                    'auto_rebid' => $tender->auto_rebid,
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

                $tender->update([
                    'auto_rebid' => false,
                    'status' => 'closed',
                    'is_finished' => true,
                    'finished_at' => $now,
                ]);

                $log->business('tender_auto_retender_created', 'Автоматически создан новый раунд тендера (переторжка)', [
                    'source_tender_id' => $tender->id,
                    'new_tender_id' => $newTender->id,
                    'round' => $newTender->round_number,
                ]);
            });
        }
    }
}
