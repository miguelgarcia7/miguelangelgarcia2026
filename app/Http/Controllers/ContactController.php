<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessage;
use App\Services\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    /**
     * Handle a contact form submission.
     */
    public function send(ContactRequest $request, Recaptcha $recaptcha): RedirectResponse
    {
        $assessment = $recaptcha->assess(
            $request->string('recaptcha_token')->toString(),
            expectedAction: 'contact',
            ip: $request->ip(),
        );

        if ($assessment->shouldBlock()) {
            Log::warning('Contact form submission blocked by reCAPTCHA', [
                'score' => $assessment->score,
                'submission' => $request->validated(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['message' => 'Your message looked automated to our spam check. Please try again.']);
        }

        try {
            Mail::to(config('portfolio.contact_email'))->send(new ContactMessage(
                senderName: $request->validated('name'),
                senderEmail: $request->validated('email'),
                body: $request->validated('message'),
                assessment: $assessment,
            ));
        } catch (Throwable $e) {
            // Log the submission itself so a delivery outage never loses a
            // message, and tell the sender rather than pretending it sent.
            Log::error('Contact form delivery failed', [
                'exception' => $e->getMessage(),
                'submission' => $request->validated(),
            ]);

            return redirect()->to('/#contact')
                ->withInput()
                ->with('contact.failed', true);
        }

        return redirect()->to('/#contact')
            ->with('contact.sent', $request->senderFirstName());
    }
}
