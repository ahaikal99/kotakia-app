<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public int $tries = 5;

    public int $timeout = 45;

    public function __construct(public string $customerName)
    {
        $this->onConnection('database')->onQueue('receipts')->beforeCommit();
    }

    public function backoff(): array
    {
        return [60, 300, 900, 1800];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('kotakiahq@gmail.com', 'Kotakia'),
            replyTo: [new Address('kotakiahq@gmail.com', 'Kotakia')],
            subject: 'Selamat datang ke Kotakia — akaun anda sedia digunakan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            text: 'emails.welcome-text',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
