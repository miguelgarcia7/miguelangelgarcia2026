<?php

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;

test('a valid submission emails the site owner', function () {
    Mail::fake();
    config(['portfolio.contact_email' => 'owner@example.com']);

    $this->from('/')->post('/contact', [
        'name' => 'Ana Torres',
        'email' => 'ana@example.com',
        'message' => 'I have a project I would like to discuss with you.',
    ]);

    Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) {
        $envelope = $mail->envelope();

        return $mail->hasTo('owner@example.com')
            && $mail->senderName === 'Ana Torres'
            && $mail->senderEmail === 'ana@example.com'
            && $envelope->replyTo[0]->address === 'ana@example.com'
            && str_contains($envelope->subject, 'Ana Torres');
    });
});

test('the email renders the submission as html', function () {
    $rendered = (new ContactMessage(
        senderName: 'Ana Torres',
        senderEmail: 'ana@example.com',
        body: "First line.\nSecond line.",
    ))->render();

    expect($rendered)
        ->toContain('Ana Torres')
        ->toContain('mailto:ana@example.com')
        ->toContain('First line.<br />')
        ->toContain('New message from the contact form');
});

test('the email escapes html in the message body', function () {
    $rendered = (new ContactMessage(
        senderName: 'Ana Torres',
        senderEmail: 'ana@example.com',
        body: '<script>alert(1)</script>',
    ))->render();

    expect($rendered)
        ->not->toContain('<script>alert(1)</script>')
        ->toContain('&lt;script&gt;');
});

test('a delivery failure keeps the message and warns the sender', function () {
    Mail::shouldReceive('to->send')->andThrow(new RuntimeException('Postmark down'));

    $response = $this->from('/')->post('/contact', [
        'name' => 'Ana Torres',
        'email' => 'ana@example.com',
        'message' => 'I have a project I would like to discuss with you.',
    ]);

    $response
        ->assertRedirect('/#contact')
        ->assertSessionHas('contact.failed', true)
        ->assertSessionMissing('contact.sent');
});

test('a valid submission flashes a success message', function () {
    Mail::fake();

    $response = $this->from('/')->post('/contact', [
        'name' => 'Ana Torres',
        'email' => 'ana@example.com',
        'message' => 'I have a project I would like to discuss with you.',
    ]);

    $response
        ->assertRedirect('/#contact')
        ->assertSessionHas('contact.sent', 'Ana');

    $this->followingRedirects()
        ->withSession(['contact.sent' => 'Ana'])
        ->get('/')
        ->assertSee('Message sent');
});

test('all fields are required', function () {
    $response = $this->from('/')->post('/contact', []);

    $response->assertInvalid([
        'name' => 'Please enter your name.',
        'email' => 'Please enter your email.',
        'message' => 'Please write a short message.',
    ]);
});

test('the email must be valid', function () {
    $response = $this->from('/')->post('/contact', [
        'name' => 'Ana Torres',
        'email' => 'not-an-email',
        'message' => 'I have a project I would like to discuss with you.',
    ]);

    $response->assertInvalid([
        'email' => 'That doesn’t look like a valid email.',
    ]);
});

test('the message must be at least ten characters', function () {
    $response = $this->from('/')->post('/contact', [
        'name' => 'Ana Torres',
        'email' => 'ana@example.com',
        'message' => 'short',
    ]);

    $response->assertInvalid([
        'message' => 'A little more detail, please (10+ characters).',
    ]);
});

test('a fetch submit gets the success panel as json, not a redirect', function () {
    Mail::fake();

    $response = $this->postJson('/contact', [
        'name' => 'Ana Torres',
        'email' => 'ana@example.com',
        'message' => 'I have a project I would like to discuss with you.',
    ]);

    $response->assertOk();
    expect($response->json('html'))
        ->toContain('Message sent')
        ->toContain('Thanks, Ana');
});

test('a fetch submit gets validation errors as json', function () {
    Mail::fake();

    $this->postJson('/contact', ['name' => '', 'email' => 'nope', 'message' => 'short'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'message'])
        ->assertJsonPath('errors.email.0', 'That doesn’t look like a valid email.');

    Mail::assertNothingSent();
});

test('a fetch submit reports a delivery failure with a 502', function () {
    Mail::shouldReceive('to->send')->andThrow(new RuntimeException('Postmark down'));

    $this->postJson('/contact', [
        'name' => 'Ana Torres',
        'email' => 'ana@example.com',
        'message' => 'I have a project I would like to discuss with you.',
    ])->assertStatus(502);
});

test('validation errors redirect back to the contact section', function () {
    $response = $this->from('/')->post('/contact', []);

    $response->assertRedirect('/#contact');
});

test('invalid fields are marked in the rendered form', function () {
    $this->from('/')->post('/contact', []);

    $this->followingRedirects()
        ->from('/')
        ->post('/contact', [])
        ->assertSee('border-danger', escape: false)
        ->assertSee('Please enter your name.');
});
