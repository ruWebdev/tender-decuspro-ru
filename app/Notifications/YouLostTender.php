<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YouLostTender extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param array<int, array{title:string,price:float}> $items
     */
    public function __construct(
        public Tender $tender,
        public float $bestTotal,
        public array $items,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Тендер "' . $this->tender->title . '" завершён — вы не победили')
            ->line('Тендер "' . $this->tender->title . '" завершён, и ваше предложение не стало победителем.')
            ->line('Итоговая лучшая цена: ' . number_format($this->bestTotal, 2, '.', ' ') . ' ₽')
            ->line('Список позиций победителя (без раскрытия поставщика):');

        foreach ($this->items as $item) {
            $mail->line(
                '- ' . $item['title'] . ' — ' .
                    'цена: ' . number_format($item['price'], 2, '.', ' ') . ' ₽'
            );
        }

        return $mail;
    }
}
