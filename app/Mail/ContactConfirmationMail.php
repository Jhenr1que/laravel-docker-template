<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactConfirmationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $contactData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('site.mail.confirmation_subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-confirmation',
            with: ['contact' => $this->contactData],
        );
    }
}