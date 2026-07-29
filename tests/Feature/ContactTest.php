<?php

test('a valid submission flashes a success message', function () {
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
