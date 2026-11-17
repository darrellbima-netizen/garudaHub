<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BidController;

Route::get('/', function () {
    return view('welcome');
});

// Routing Project
Route::resource('projects', ProjectController::class);

// Routing Bid
Route::resource('bids', BidController::class);
