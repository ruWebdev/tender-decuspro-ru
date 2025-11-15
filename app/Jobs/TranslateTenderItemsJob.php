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
            $name = $item->title;

            $item->name_en = $translator->translate($name, 'en');
            $item->name_cn = $translator->translate($name, 'zh');
            $item->save();
        }
    }
}
