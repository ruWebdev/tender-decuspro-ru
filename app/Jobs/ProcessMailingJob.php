<?php

namespace App\Jobs;

use App\Models\Mailing;
use App\Models\MailingLog;
use App\Models\PlatformSupplier;
use App\Models\Tender;
use App\Services\SmtpBzService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMailingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Максимальное количество писем в час для всех рассылок
     */
    public const MAX_EMAILS_PER_HOUR = 50;

    public function handle(SmtpBzService $smtpBzService): void
    {
        // Получаем количество писем, отправленных за последний час
        $sentLastHour = MailingLog::where('created_at', '>=', now()->subHour())
            ->where('status', MailingLog::STATUS_SENT)
            ->count();

        $remainingQuota = self::MAX_EMAILS_PER_HOUR - $sentLastHour;

        if ($remainingQuota <= 0) {
            Log::info('ProcessMailingJob: достигнут лимит писем в час', [
                'sent_last_hour' => $sentLastHour,
                'limit' => self::MAX_EMAILS_PER_HOUR,
            ]);

            return;
        }

        // Получаем активные рассылки
        $mailings = Mailing::where('status', Mailing::STATUS_RUNNING)
            ->with('notificationTemplate')
            ->get();

        if ($mailings->isEmpty()) {
            return;
        }

        $emailsSentThisRun = 0;

        foreach ($mailings as $mailing) {
            if ($emailsSentThisRun >= $remainingQuota) {
                break;
            }

            $emailsSentThisRun += $this->processMailing($mailing, $smtpBzService, $remainingQuota - $emailsSentThisRun);
        }

        Log::info('ProcessMailingJob: завершено', [
            'emails_sent' => $emailsSentThisRun,
        ]);
    }

    /**
     * Обработать одну рассылку
     */
    private function processMailing(Mailing $mailing, SmtpBzService $smtpBzService, int $quota): int
    {
        $template = $mailing->notificationTemplate;

        if (! $template) {
            Log::warning('ProcessMailingJob: шаблон не найден', ['mailing_id' => $mailing->id]);
            $mailing->update(['status' => Mailing::STATUS_PAUSED]);

            return 0;
        }

        // Получаем ID поставщиков, которым уже отправлено письмо в этой рассылке
        $sentSupplierIds = MailingLog::where('mailing_id', $mailing->id)
            ->pluck('platform_supplier_id')
            ->toArray();

        // Получаем поставщиков для рассылки
        $query = PlatformSupplier::whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotIn('id', $sentSupplierIds);

        // Применяем фильтр по ключевым словам
        if ($mailing->company_filter) {
            $keywords = array_map('trim', explode(',', $mailing->company_filter));
            $keywords = array_filter($keywords);

            if (! empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('name', 'LIKE', '%' . $keyword . '%');
                    }
                });
            }
        }

        $suppliers = $query->limit($quota)->get();

        if ($suppliers->isEmpty()) {
            // Нет больше получателей — завершаем рассылку
            $mailing->update(['status' => Mailing::STATUS_COMPLETED]);
            Log::info('ProcessMailingJob: рассылка завершена, нет получателей', ['mailing_id' => $mailing->id]);

            return 0;
        }

        // Подготавливаем список тендеров для замены {{TendersList}}
        $tendersHtml = $this->buildTendersListHtml($mailing->tender_ids ?? []);

        $emailsSent = 0;

        foreach ($suppliers as $supplier) {
            // Проверяем, не отправляли ли уже этому поставщику в рамках другой активной рассылки
            $alreadySentToday = MailingLog::where('platform_supplier_id', $supplier->id)
                ->where('created_at', '>=', now()->startOfDay())
                ->exists();

            if ($alreadySentToday) {
                continue;
            }

            // Получаем тело письма на языке рассылки
            $locale = $mailing->language ?: 'ru';
            $bodyField = 'body_' . $locale;
            $body = $template->{$bodyField} ?: $template->body_ru;

            if (! $body) {
                continue;
            }

            // Заменяем {{TendersList}} на список тендеров
            $body = str_replace('{{TendersList}}', $tendersHtml, $body);

            // Отправляем письмо
            $result = $smtpBzService->send(
                $supplier->email,
                $template->name,
                $body
            );

            // Логируем результат
            MailingLog::create([
                'mailing_id' => $mailing->id,
                'platform_supplier_id' => $supplier->id,
                'email' => $supplier->email,
                'status' => $result['success'] ? MailingLog::STATUS_SENT : MailingLog::STATUS_FAILED,
                'error_message' => $result['error'] ?? null,
            ]);

            if ($result['success']) {
                $emailsSent++;
                $supplier->update(['last_mailing_at' => now()]);
            }

            // Обновляем счётчик отправленных писем
            $mailing->increment('sent_count');

            // Проверяем лимит рассылки
            if ($mailing->emails_limit > 0 && $mailing->sent_count >= $mailing->emails_limit) {
                $mailing->update(['status' => Mailing::STATUS_COMPLETED]);
                Log::info('ProcessMailingJob: достигнут лимит рассылки', [
                    'mailing_id' => $mailing->id,
                    'sent_count' => $mailing->sent_count,
                ]);

                break;
            }
        }

        return $emailsSent;
    }

    /**
     * Сформировать HTML-список тендеров
     */
    private function buildTendersListHtml(array $tenderIds): string
    {
        if (empty($tenderIds)) {
            return '';
        }

        $tenders = Tender::whereIn('id', $tenderIds)->get();

        if ($tenders->isEmpty()) {
            return '';
        }

        $html = '<ul>';

        foreach ($tenders as $tender) {
            $url = route('tenders.show', $tender->id);
            $title = e($tender->title);
            $html .= "<li><a href=\"{$url}\">{$title}</a></li>";
        }

        $html .= '</ul>';

        return $html;
    }
}
