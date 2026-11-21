<?php

// NIM: 252410102013
// Nama: Darrell Bima Nusantara Perkasa
// Project: GarudaHub PWEB

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BidController;

Route::pattern('project', '[0-9]+');
Route::pattern('bid', '[0-9]+');

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Authentication
Route::middleware('guest')->group(function () {

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// API yang membutuhkan autentikasi
Route::prefix('api')
    ->middleware('auth')
    ->group(function () {

        Route::apiResource('projects', ProjectController::class);

        Route::apiResource('bids', BidController::class);

    });
