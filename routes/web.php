<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes Configuration - ZIVO PAY
|--------------------------------------------------------------------------
*/

// Public Root Renders Website Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('reset-password', [PasswordResetController::class, 'showForm'])
    ->name('password.reset.form');
Route::post('reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:10,1')->name('password.reset.submit');
Route::get('password-reset-success', [PasswordResetController::class, 'showSuccess'])->name('password.reset.success');

// Public Mobile App WebViews & Static Pages
Route::get('page/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('terms-conditions', [PageController::class, 'show'])->defaults('slug', 'terms-conditions');
Route::get('terms', [PageController::class, 'show'])->defaults('slug', 'terms-conditions');
Route::get('privacy-policy', [PageController::class, 'show'])->defaults('slug', 'privacy-policy');
Route::get('privacy', [PageController::class, 'show'])->defaults('slug', 'privacy-policy');
Route::get('contact-us', [PageController::class, 'show'])->defaults('slug', 'contact-us');
Route::get('about-us', [PageController::class, 'show'])->defaults('slug', 'about-us');
Route::get('refund-policy', [PageController::class, 'show'])->defaults('slug', 'refund-policy');
Route::get('grievance-redressal', [PageController::class, 'show'])->defaults('slug', 'grievance-redressal');

// Load Modular Admin & User Route Files
require __DIR__ . '/admin.php';
require __DIR__ . '/user.php';