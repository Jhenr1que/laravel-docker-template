<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public array $contactData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contactData['email'], $this->contactData['name'])],
            subject: __('site.mail.contact_subject', ['name' => $this->contactData['name']]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: ['contact' => $this->contactData],
        );
    }
}