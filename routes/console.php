<?php

use App\Jobs\GenerateAutoTenderJob;
use App\Jobs\AutoRetenderJob;
use App\Jobs\TranslateTendersJob;
use App\Jobs\TranslateNotificationTemplatesJob;
use App\Jobs\UpdateCnyRubRateJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tenders:generate-auto {count=1}', function (int $count) {
    for ($i = 0; $i < $count; $i++) {
        $this->info('Запуск генерации тендера через DeepSeek (попытка ' . ($i + 1) . ')');
        GenerateAutoTenderJob::dispatch();
    }

    $this->info('Команда генерации тендеров отправлена в очередь.');
})->purpose('Ручной запуск генерации тендеров через DeepSeek');

Schedule::job(GenerateAutoTenderJob::class)->hourly();
Schedule::job(AutoRetenderJob::class)->everyFiveMinutes();
Schedule::job(TranslateTendersJob::class)->hourly();
Schedule::job(TranslateNotificationTemplatesJob::class)->hourly();
Schedule::job(UpdateCnyRubRateJob::class)->daily();
