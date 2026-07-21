<?php

test('the home page renders successfully', function () {
    $response = $this->get('/');

    $response->assertOk();
});

test('the home page content is visible without javascript', function () {
    $response = $this->get('/');

    $response
        ->assertSee('Miguel Angel Garcia')
        ->assertSee('Senior Full-Stack Engineer')
        ->assertSee('Building beautiful')
        ->assertSee('About me')
        ->assertSee('Experience')
        ->assertSee('Director of Web Development')
        ->assertSee('RealPage')
        ->assertSee('AT&amp;T', escape: false)
        ->assertSee('Harley-Davidson')
        ->assertSee('Projects')
        ->assertSee('QCP Staffing / Minute')
        ->assertSee('Appointment Hub')
        ->assertSee('Claude Code')
        ->assertSee('CI/CD')
        ->assertSee('Languages &amp; Frameworks', escape: false)
        ->assertSee("Let's build something together.", escape: false);
});

test('the home page includes person structured data', function () {
    $response = $this->get('/');

    $response
        ->assertSee('application/ld+json', escape: false)
        ->assertSee('"@context":"https://schema.org"', escape: false)
        ->assertSee('"@type":"Person"', escape: false)
        ->assertSee('"name":"Miguel Angel Garcia"', escape: false);
});

test('the home page includes seo meta tags', function () {
    $response = $this->get('/');

    $response
        ->assertSee('<meta name="description"', escape: false)
        ->assertSee('<link rel="canonical"', escape: false)
        ->assertSee('<meta property="og:title"', escape: false);
});
