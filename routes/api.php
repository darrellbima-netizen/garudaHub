<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BidController;

// API Project
Route::apiResource('projects', ProjectController::class);

// API Bid
Route::apiResource('bids', BidController::class);
