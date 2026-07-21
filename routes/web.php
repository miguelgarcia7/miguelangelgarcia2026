<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::post('contact', [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

Route::get('sitemap.xml', function () {
    return response()
        ->view('sitemap', [
            'urls' => [
                ['loc' => route('home'), 'lastmod' => '2026-07-14'],
            ],
        ])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
