<?php

namespace App\Notifications;

use App\Models\Tender;
use App\Models\TenderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceOutbid extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Tender $tender,
        public TenderItem $item,
        public float $oldPrice,
        public float $newPrice,
    ) {}

    /**
     * Каналы доставки уведомления.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Формирование email-сообщения.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Вас перебили в закупке "' . $this->tender->title . '"')
            ->line('Позиция: ' . $this->item->title)
            ->line('Ваша цена: ' . number_format($this->oldPrice, 2, '.', ' '))
            ->line('Новая лучшая цена: ' . number_format($this->newPrice, 2, '.', ' '));
    }
}
