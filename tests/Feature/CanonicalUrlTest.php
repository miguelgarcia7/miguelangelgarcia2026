<?php

beforeEach(function () {
    $this->app['env'] = 'production';
    config(['app.url' => 'https://miguelangelgarcia.com']);
});

test('http, www and other hosts redirect to the canonical https url', function (string $from) {
    $this->get($from)
        ->assertStatus(301)
        ->assertRedirect('https://miguelangelgarcia.com/sitemap.xml?x=1');
})->with([
    'http' => 'http://miguelangelgarcia.com/sitemap.xml?x=1',
    'http www' => 'http://www.miguelangelgarcia.com/sitemap.xml?x=1',
    'https www' => 'https://www.miguelangelgarcia.com/sitemap.xml?x=1',
]);

test('non-read requests keep their method with a 308', function () {
    $this->post('http://www.miguelangelgarcia.com/contact')
        ->assertStatus(308)
        ->assertRedirect('https://miguelangelgarcia.com/contact');
});

test('the canonical url is served normally', function () {
    $this->get('https://miguelangelgarcia.com/')
        ->assertOk()
        ->assertSee('<link rel="canonical" href="https://miguelangelgarcia.com">', false);
});

test('nothing is redirected outside production', function () {
    $this->app['env'] = 'local';

    $this->get('http://www.miguelangelgarcia.com/')->assertOk();
});
