<?php

namespace App\Mail;

use App\Services\RecaptchaAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $body,
        public ?RecaptchaAssessment $assessment = null,
    ) {}

    /**
     * Get the message envelope.
     *
     * Sent from the site's own verified address — Postmark rejects mail
     * whose "from" is not a confirmed sender signature — with the visitor
     * as reply-to, so replying from the inbox reaches them directly.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New message from {$this->senderName} · miguelgarcia.site",
            replyTo: [new Address($this->senderEmail, $this->senderName)],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-message',
        );
    }
}
