<?php

namespace App\Jobs;

use App\Models\Tender;
use App\Models\TenderItem;
use App\Services\TranslatorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateTenderItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Tender $tender) {}

    public function handle(TranslatorService $translator): void
    {
        $tender = $this->tender->fresh();

        if (! $tender) {
            return;
        }

        $tender->loadMissing('items');

        /** @var TenderItem $item */
        foreach ($tender->items as $item) {
            $name = $item->getRawOriginal('title');

            if ($name === null || $name === '') {
                continue;
            }

            $changed = false;

            if (! $item->name_en) {
                $item->name_en = $translator->translate($name, 'en');
                $changed = true;
            }

            if (! $item->name_cn) {
                $item->name_cn = $translator->translate($name, 'zh');
                $changed = true;
            }

            if ($changed) {
                $item->save();
            }
        }
    }
}
