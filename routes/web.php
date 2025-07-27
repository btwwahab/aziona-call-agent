<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallController;

Route::get('/', function () {
    return view('dashboard');
});

// Dashboard AJAX endpoints
Route::get('/dashboard/data', [CallController::class, 'dashboardData']);
Route::post('/call', [CallController::class, 'placeCall']);
Route::post('/schedule', [CallController::class, 'scheduleCall']);
// Route moved to routes/api.php for proper webhook handling