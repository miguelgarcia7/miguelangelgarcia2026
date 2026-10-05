<?php

test('the google tag renders when a measurement id is set', function () {
    config(['services.google_analytics.measurement_id' => 'G-TEST123']);

    $this->get('/')
        ->assertOk()
        ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST123', false)
        ->assertSee("gtag('config', 'G-TEST123')", false);
});

test('the google tag is left out without a measurement id', function () {
    config(['services.google_analytics.measurement_id' => null]);

    $this->get('/')
        ->assertOk()
        ->assertDontSee('googletagmanager.com', false);
});
