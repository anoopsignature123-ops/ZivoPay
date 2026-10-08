<?php

use App\Http\Controllers\PageController;
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

// Public Mobile App WebViews & Static Pages
Route::get('page/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('terms-conditions', [PageController::class, 'show'])->defaults('slug', 'terms-conditions');
Route::get('terms', [PageController::class, 'show'])->defaults('slug', 'terms-conditions');
Route::get('privacy-policy', [PageController::class, 'show'])->defaults('slug', 'privacy-policy');
Route::get('privacy', [PageController::class, 'show'])->defaults('slug', 'privacy-policy');
Route::get('contact-us', [PageController::class, 'show'])->defaults('slug', 'contact-us');
Route::get('about-us', [PageController::class, 'show'])->defaults('slug', 'about-us');
Route::get('refund-policy', [PageController::class, 'show'])->defaults('slug', 'refund-policy');

// Load Modular Admin & User Route Files
require __DIR__.'/admin.php';
require __DIR__.'/user.php';
