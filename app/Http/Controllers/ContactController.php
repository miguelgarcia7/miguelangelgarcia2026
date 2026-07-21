<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Handle a contact form submission.
     */
    public function send(ContactRequest $request): RedirectResponse
    {
        // TODO: deliver the message (e.g. a Mailable to your inbox) once a
        // mailer is configured. Logged for now so submissions aren't lost.
        Log::info('Contact form submission', $request->validated());

        return redirect()
            ->to('/#contact')
            ->with('contact.sent', $request->senderFirstName());
    }
}
