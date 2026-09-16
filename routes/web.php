<?php

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

// Load Modular Admin & User Route Files
require __DIR__.'/admin.php';
require __DIR__.'/user.php';
