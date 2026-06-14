<?php

use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/queue-status', [PatientDashboardController::class, 'index']);
});
