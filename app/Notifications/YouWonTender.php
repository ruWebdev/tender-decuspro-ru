<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YouWonTender extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param array<int, array{title:string,quantity:float,price:float,sum:float}> $items
     */
    public function __construct(
        public Tender $tender,
        public float $total,
        public array $items,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Вы выиграли тендер "' . $this->tender->title . '"')
            ->line('Вы были выбраны победителем в тендере "' . $this->tender->title . '".')
            ->line('Итоговая сумма вашего предложения: ' . number_format($this->total, 2, '.', ' ') . ' ₽')
            ->line('Детализация по позициям:');

        foreach ($this->items as $item) {
            $mail->line(
                '- ' . $item['title'] . ' — ' .
                    'кол-во: ' . $item['quantity'] . ', ' .
                    'цена: ' . number_format($item['price'], 2, '.', ' ') . ' ₽, ' .
                    'сумма: ' . number_format($item['sum'], 2, '.', ' ') . ' ₽'
            );
        }

        return $mail;
    }
}
