<?php

namespace App\Jobs;

use App\Jobs\TranslateTenderJob;
use App\Services\TenderGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAutoTenderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(TenderGeneratorService $generator): void
    {
        $tender = $generator->generate();

        if ($tender) {
            Log::info('Автоматически создан тендер через DeepSeek', [
                'tender_id' => $tender->id,
            ]);

            TranslateTenderJob::dispatch($tender);
        } else {
            Log::warning('GenerateAutoTenderJob: не удалось создать тендер');
        }
    }
}
