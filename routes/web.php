<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/docs', [App\Http\Controllers\DocController::class, 'index'])->name('docs.index');
    Route::get('/docs/{slug}', [App\Http\Controllers\DocController::class, 'show'])->name('docs.show')->where('slug', '[a-z0-9\-]+');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Stripe webhook (opt-in: set STRIPE_WEBHOOK_SECRET). Excluded from CSRF in bootstrap/app.php.
Route::post('stripe/webhook', App\Http\Controllers\StripeWebhookController::class)->name('stripe.webhook');
