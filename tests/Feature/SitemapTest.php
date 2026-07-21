<?php

test('the sitemap lists the home page', function () {
    $response = $this->get('/sitemap.xml');

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('<urlset', escape: false)
        ->assertSee(route('home'), escape: false);
});
