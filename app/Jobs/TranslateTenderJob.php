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

        $originalDescription = $tender->getRawOriginal('description');
        $originalTitle = $tender->getRawOriginal('title');

        if ($originalDescription !== null && $originalDescription !== '') {
            if (! $tender->description_en) {
                $tender->description_en = $translator->translate($originalDescription, 'en');
            }

            if (! $tender->description_cn) {
                $tender->description_cn = $translator->translate($originalDescription, 'zh');
            }
        }

        if ($originalTitle !== null && $originalTitle !== '') {
            if (! $tender->title_en) {
                $tender->title_en = $translator->translate($originalTitle, 'en');
            }

            if (! $tender->title_cn) {
                $tender->title_cn = $translator->translate($originalTitle, 'zh');
            }
        }

        $tender->save();

        TranslateTenderItemsJob::dispatch($tender);
    }
}
