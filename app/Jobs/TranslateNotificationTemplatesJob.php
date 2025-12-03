<?php

namespace App\Jobs;

use App\Models\NotificationTemplate;
use App\Services\NotificationTemplateTranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateNotificationTemplatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationTemplateTranslateService $translator): void
    {
        $query = NotificationTemplate::query()
            ->where('needs_translation', true)
            ->whereNotNull('body_ru')
            ->where('body_ru', '!=', '');

        $total = $query->count();

        if ($total === 0) {
            return;
        }

        $processed = 0;
        $translated = 0;

        $query->orderBy('id')
            ->chunkById(50, function ($templates) use ($translator, &$processed, &$translated): void {
                foreach ($templates as $template) {
                    $processed++;

                    $result = $translator->translate(
                        $template->body_ru,
                        $template->body_en,
                        $template->body_cn,
                    );

                    if (! $result) {
                        continue;
                    }

                    $template->body_ru = $result['body_ru'];
                    $template->body_en = $result['body_en'];
                    $template->body_cn = $result['body_cn'];
                    $template->needs_translation = false;
                    $template->save();

                    $translated++;
                }
            });

        Log::info('TranslateNotificationTemplatesJob: автоперевод шаблонов уведомлений завершён', [
            'total_pending' => $total,
            'processed' => $processed,
            'translated' => $translated,
        ]);
    }
}
