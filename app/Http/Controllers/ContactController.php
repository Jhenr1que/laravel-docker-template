<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactConfirmationMail;
use App\Mail\ContactMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(ContactRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $recipientEmail = config('contact.recipient.email', config('mail.from.address'));
        $recipientName = config('contact.recipient.name', config('app.name'));

        Mail::to([$recipientEmail => $recipientName])->send(new ContactMail($payload));

        Mail::to($payload['email'], $payload['name'])
            ->send(new ContactConfirmationMail($payload));

        return redirect()
            ->route('contact')
            ->with('status', __('site.contact.feedback.success'));
    }
}