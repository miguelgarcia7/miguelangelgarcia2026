<?php

use App\Mail\ContactMessage;
use App\Services\RecaptchaAssessment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    config([
        'services.recaptcha.secret_key' => 'test-secret',
        'services.recaptcha.suspicious_below' => 0.5,
        'services.recaptcha.block_below' => null,
    ]);
});

function fakeVerify(array $body): void
{
    Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response($body)]);
}

function submit(array $overrides = []): TestResponse
{
    return test()->from('/')->post('/contact', array_merge([
        'name' => 'Ana Torres',
        'email' => 'ana@example.com',
        'message' => 'I have a project I would like to discuss with you.',
        'recaptcha_token' => 'token-from-browser',
    ], $overrides));
}

test('a good score sends the message without a spam warning', function () {
    Mail::fake();
    fakeVerify(['success' => true, 'score' => 0.9, 'action' => 'contact']);

    submit()->assertSessionHas('contact.sent');

    Mail::assertSent(ContactMessage::class, fn (ContactMessage $m) => $m->assessment->verified
        && $m->assessment->score === 0.9
        && ! $m->assessment->isSuspicious());
});

test('a low score still sends but is flagged as suspicious', function () {
    Mail::fake();
    fakeVerify(['success' => true, 'score' => 0.1, 'action' => 'contact']);

    submit()->assertSessionHas('contact.sent');

    Mail::assertSent(ContactMessage::class, fn (ContactMessage $m) => $m->assessment->isSuspicious());
});

test('a missing token still sends, marked unverified', function () {
    Mail::fake();

    submit(['recaptcha_token' => ''])->assertSessionHas('contact.sent');

    Mail::assertSent(ContactMessage::class, fn (ContactMessage $m) => ! $m->assessment->verified
        && str_contains($m->assessment->reason, 'no token'));
});

test('google being unreachable still sends the message', function () {
    Mail::fake();
    Http::fake(['www.google.com/*' => Http::response('', 503)]);

    submit()->assertSessionHas('contact.sent');

    Mail::assertSent(ContactMessage::class, fn (ContactMessage $m) => ! $m->assessment->verified);
});

test('a token minted for another action is not trusted', function () {
    Mail::fake();
    fakeVerify(['success' => true, 'score' => 0.9, 'action' => 'login']);

    submit()->assertSessionHas('contact.sent');

    Mail::assertSent(ContactMessage::class, fn (ContactMessage $m) => ! $m->assessment->verified
        && $m->assessment->reason === 'action mismatch');
});

test('submissions are blocked only when a block threshold is configured', function () {
    Mail::fake();
    config(['services.recaptcha.block_below' => 0.3]);
    fakeVerify(['success' => true, 'score' => 0.1, 'action' => 'contact']);

    submit()->assertSessionHasErrors('message');

    Mail::assertNothingSent();
});

test('the site key is never sent to the browser as a secret', function () {
    config(['services.recaptcha.site_key' => 'site-key-123']);

    $this->get('/')
        ->assertSee('recaptcha/api.js?render=site-key-123', escape: false)
        ->assertSee('data-recaptcha-action="contact"', escape: false)
        ->assertDontSee('test-secret');
});

test('the email shows the spam check result when unverified', function () {
    $rendered = (new ContactMessage(
        senderName: 'Ana Torres',
        senderEmail: 'ana@example.com',
        body: 'Hello',
        assessment: RecaptchaAssessment::unverified('no token from the browser'),
    ))->render();

    expect($rendered)->toContain('Spam check')->toContain('Not verified');
});

test('reCAPTCHA is skipped entirely when no secret is configured', function () {
    config(['services.recaptcha.secret_key' => null]);
    Http::fake();
    Mail::fake();

    submit()->assertSessionHas('contact.sent');

    Http::assertNothingSent();
});
