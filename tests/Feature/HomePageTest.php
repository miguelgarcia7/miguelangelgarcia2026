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
        ->assertSee("Let's connect.", escape: false);
});

test('the home page includes person structured data', function () {
    $response = $this->get('/');

    $response
        ->assertSee('application/ld+json', escape: false)
        ->assertSee('"@context":"https://schema.org"', escape: false)
        ->assertSee('"@type":"Person"', escape: false)
        ->assertSee('"name":"Miguel Angel Garcia"', escape: false);
});

test('social profiles are linked for people and for crawlers', function () {
    $response = $this->get('/');

    // Visible links for recruiters…
    $response
        ->assertSee('https://github.com/miguelgarcia7', escape: false)
        ->assertSee('https://www.linkedin.com/in/miguelgarcia7', escape: false);

    // …and the sameAs graph that ties the profiles to this person.
    foreach (config('portfolio.same_as') as $profile) {
        $response->assertSee('"'.$profile.'"', escape: false);
    }
});

test('the page carries the accessibility scaffolding', function () {
    $response = $this->get('/');

    $response
        ->assertSee('class="skip-link"', escape: false)   // 2.4.1 bypass blocks
        ->assertSee('href="#main"', escape: false)
        ->assertSee('<main id="main"', escape: false)
        ->assertSee('<html lang="en"', escape: false)     // 3.1.1 language
        ->assertSee('<nav', escape: false)                // 1.3.1 landmarks
        ->assertSee('<footer', escape: false);

    // 2.4.7: the focus indicator must never be suppressed on form fields.
    expect($response->getContent())->not->toContain('focus:outline-none');
});

test('each section is a focusable landmark named by its heading', function () {
    $content = $this->get('/')->getContent();

    foreach (['about', 'experience', 'projects', 'stack', 'contact'] as $section) {
        // tabindex="-1" lets an in-page link move focus here, so the next
        // Tab continues inside the section rather than back in the nav.
        expect($content)
            ->toContain('id="'.$section.'" data-reveal tabindex="-1" aria-labelledby="'.$section.'-heading"')
            ->toContain('id="'.$section.'-heading"');
    }
});

test('validation errors are wired to their field for screen readers', function () {
    $this->from('/')->post('/contact', ['name' => '', 'email' => '', 'message' => '']);

    $this->get('/')
        ->assertSee('aria-invalid="true"', escape: false)                    // 4.1.2
        ->assertSee('aria-describedby="contact-name-error"', escape: false)  // 3.3.1
        ->assertSee('id="contact-name-error"', escape: false);
});

test('the home page includes seo meta tags', function () {
    $response = $this->get('/');

    $response
        ->assertSee('<meta name="description"', escape: false)
        ->assertSee('<link rel="canonical"', escape: false)
        ->assertSee('<meta property="og:title"', escape: false);
});
