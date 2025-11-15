<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Тема письма.
     */
    public string $subjectText;

    /**
     * Основной текст письма.
     */
    public string $content;

    public function __construct(string $subjectText, string $content)
    {
        $this->subjectText = $subjectText;
        $this->content = $content;
    }

    /**
     * Построение сообщения.
     */
    public function build(): self
    {
        return $this
            ->subject($this->subjectText)
            ->markdown('emails.base', [
                'content' => $this->content,
            ]);
    }
}
