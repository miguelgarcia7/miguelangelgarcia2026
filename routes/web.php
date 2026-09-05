<?php

use App\Http\Controllers\Admin\AiKnowledgeController;
use App\Http\Controllers\Admin\AiQuestionLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PortfolioAiController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

Route::post('contact', [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

// "Ask about me" assistant. Limits live in config/ai.php.
Route::post('ask', [PortfolioAiController::class, 'ask'])
    ->middleware('throttle:ask')
    ->name('ask');

Route::get('sitemap.xml', function () {
    return response()
        ->view('sitemap', [
            'urls' => [
                ['loc' => route('home'), 'lastmod' => '2026-09-05'],
            ],
        ])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
|
| Sign-in is plain Blade; everything behind it is Inertia + React. The
| Inertia middleware is scoped here so the public site never touches it.
|
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login')->name('login.attempt');
    });

    Route::middleware(['auth', HandleInertiaRequests::class])->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::redirect('/', '/admin/ai-knowledge');

        Route::get('ai-knowledge', [AiKnowledgeController::class, 'index'])->name('knowledge.index');
        Route::get('ai-knowledge/create', [AiKnowledgeController::class, 'create'])->name('knowledge.create');
        Route::post('ai-knowledge', [AiKnowledgeController::class, 'store'])->name('knowledge.store');
        Route::get('ai-knowledge/{entry}/edit', [AiKnowledgeController::class, 'edit'])->name('knowledge.edit');
        Route::put('ai-knowledge/{entry}', [AiKnowledgeController::class, 'update'])->name('knowledge.update');
        Route::patch('ai-knowledge/{entry}/toggle', [AiKnowledgeController::class, 'toggle'])->name('knowledge.toggle');
        Route::post('ai-knowledge/{entry}/preview', [AiKnowledgeController::class, 'preview'])->name('knowledge.preview');
        Route::delete('ai-knowledge/{entry}', [AiKnowledgeController::class, 'destroy'])->name('knowledge.destroy');

        Route::get('ai-questions', [AiQuestionLogController::class, 'index'])->name('questions.index');
    });
});
