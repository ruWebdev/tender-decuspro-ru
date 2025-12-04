<?php

namespace App\Jobs;

use App\Models\SystemSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCnyRubRateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Обновить курс юаня к рублю из API ЦБ РФ и сохранить в системные настройки.
     */
    public function handle(): void
    {
        try {
            $response = Http::timeout(10)->get('https://www.cbr.ru/scripts/XML_daily.asp');

            if (! $response->ok()) {
                Log::warning('Не удалось получить XML курсов ЦБ РФ для обновления курса CNY/RUB.', [
                    'status' => $response->status(),
                ]);

                return;
            }

            $xml = @simplexml_load_string($response->body());

            if ($xml === false || ! isset($xml->Valute)) {
                Log::warning('Некорректный XML в ответе ЦБ РФ при обновлении курса CNY/RUB.');

                return;
            }

            $cny = null;

            foreach ($xml->Valute as $valute) {
                if ((string) $valute->CharCode === 'CNY') {
                    $cny = $valute;
                    break;
                }
            }

            if (! $cny) {
                Log::warning('В XML ЦБ РФ не найдена запись для CNY при обновлении курса.');

                return;
            }

            $nominal = (float) str_replace(',', '.', (string) $cny->Nominal);
            $value = (float) str_replace(',', '.', (string) $cny->Value);

            if ($nominal <= 0.0 || $value <= 0.0) {
                Log::warning('Получен некорректный номинал или значение курса CNY/RUB из ЦБ РФ.', [
                    'nominal' => (string) $cny->Nominal,
                    'value' => (string) $cny->Value,
                ]);

                return;
            }

            // Курс за 1 юань в рублях
            $rate = $value / $nominal;

            SystemSetting::setValue('cny_rub_rate', sprintf('%.6F', $rate));
            SystemSetting::setValue('cny_rub_rate_source', 'cbr_xml_daily');
            SystemSetting::setValue('cny_rub_rate_updated_at', now()->toDateTimeString());
        } catch (\Throwable $e) {
            Log::error('Ошибка при обновлении курса CNY/RUB из ЦБ РФ.', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
