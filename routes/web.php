<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

// Routing Project
Route::resource('projects', ProjectController::class);
