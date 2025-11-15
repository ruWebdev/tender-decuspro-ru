<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WinnerSelected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Tender $tender,
        public string $supplierName,
        public float $total,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Победитель выбран по тендеру "' . $this->tender->title . '"')
            ->line('По тендеру "' . $this->tender->title . '" выбран победитель.')
            ->line('Поставщик: ' . $this->supplierName)
            ->line('Итоговая сумма предложения: ' . number_format($this->total, 2, '.', ' ') . ' ₽')
            ->line('Дата завершения: ' . optional($this->tender->finished_at)->format('d.m.Y H:i'));
    }
}
