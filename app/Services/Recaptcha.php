<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class Recaptcha
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Score a submission's token.
     *
     * Deliberately fail-open: anything that stops us reaching a verdict
     * (no key, no token, network error, action mismatch) returns an
     * unverified assessment so a genuine message is never silently lost.
     */
    public function assess(?string $token, string $expectedAction, ?string $ip = null): RecaptchaAssessment
    {
        $secret = config('services.recaptcha.secret_key');

        if (blank($secret)) {
            return RecaptchaAssessment::unverified('reCAPTCHA is not configured');
        }

        if (blank($token)) {
            return RecaptchaAssessment::unverified('no token from the browser');
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(self::VERIFY_URL, array_filter([
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]));
        } catch (Throwable $e) {
            return RecaptchaAssessment::unverified('could not reach Google: '.$e->getMessage());
        }

        if (! $response->successful()) {
            return RecaptchaAssessment::unverified('Google returned HTTP '.$response->status());
        }

        $body = $response->json();

        if (! ($body['success'] ?? false)) {
            $errors = implode(', ', $body['error-codes'] ?? ['unknown error']);

            return RecaptchaAssessment::unverified($errors);
        }

        // A token minted for a different action means it was replayed from
        // elsewhere on the site, so its score says nothing about this form.
        if (($body['action'] ?? null) !== $expectedAction) {
            return RecaptchaAssessment::unverified('action mismatch');
        }

        return RecaptchaAssessment::scored((float) ($body['score'] ?? 0));
    }
}
