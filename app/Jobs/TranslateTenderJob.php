<?php

namespace App\Jobs;

use App\Models\Tender;
use App\Services\TranslatorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateTenderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Tender $tender) {}

    public function handle(TranslatorService $translator): void
    {
        $tender = $this->tender->fresh();

        if (! $tender) {
            return;
        }

        $tender->description_en = $translator->translate($tender->description, 'en');
        $tender->description_cn = $translator->translate($tender->description, 'zh');
        $tender->save();

        TranslateTenderItemsJob::dispatch($tender);
    }
}
