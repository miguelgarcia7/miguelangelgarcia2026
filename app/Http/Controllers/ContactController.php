<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessage;
use App\Services\Recaptcha;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    /**
     * Handle a contact form submission.
     *
     * Answers JSON to the script-driven submit and a redirect to a plain
     * form post, so the form keeps working with JavaScript disabled.
     */
    public function send(ContactRequest $request, Recaptcha $recaptcha): RedirectResponse|JsonResponse
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

            $error = 'Your message looked automated to our spam check. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $error,
                    'errors' => ['message' => [$error]],
                ], 422);
            }

            return back()->withInput()->withErrors(['message' => $error]);
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

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Delivery failed'], 502);
            }

            return redirect()->to('/#contact')
                ->withInput()
                ->with('contact.failed', true);
        }

        $firstName = $request->senderFirstName();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('components.portfolio.contact-sent', ['name' => $firstName])->render(),
            ]);
        }

        return redirect()->to('/#contact')->with('contact.sent', $firstName);
    }
}
