<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $messageLine;
    public ?string $actionUrl;
    public ?string $actionText;

    /**
     * @param string $subjectLine Email subject
     * @param string $messageLine Main message (plain text)
     * @param string|null $actionUrl Optional CTA link
     * @param string|null $actionText Optional CTA text
     */
    public function __construct(
        string $subjectLine,
        string $messageLine,
        ?string $actionUrl = null,
        ?string $actionText = null,
    ) {
        $this->subjectLine = $subjectLine;
        $this->messageLine = $messageLine;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user_notification',
        );
    }
}
