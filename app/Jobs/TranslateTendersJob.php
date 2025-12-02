<?php

namespace App\Jobs;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Фоновая задача для поиска и перевода всех тендеров,
 * у которых отсутствует перевод на английский или китайский язык.
 */
class TranslateTendersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Максимальное количество тендеров для обработки за один запуск.
     */
    private const LIMIT = 100;

    public function handle(): void
    {
        $query = Tender::query()
            ->where(function ($q) {
                $q->whereNull('title_en')
                    ->orWhereNull('title_cn')
                    ->orWhereNull('description_en')
                    ->orWhereNull('description_cn');
            });

        $total = $query->count();

        if ($total === 0) {
            return;
        }

        $processed = 0;

        $query->orderBy('id')
            ->limit(self::LIMIT)
            ->each(function (Tender $tender) use (&$processed): void {
                TranslateTenderJob::dispatch($tender);
                $processed++;
            });

        Log::info('TranslateTendersJob: поставлены в очередь тендеры для перевода', [
            'total_pending' => $total,
            'dispatched' => $processed,
        ]);
    }
}
