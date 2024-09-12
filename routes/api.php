<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\PerawatController;
use App\Models\Log;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/perawat', [PerawatController::class, 'store']);
Route::get('/perawats', [PerawatController::class, 'index']);
Route::get('/get-access-token', [ApiController::class, 'getAccessToken']);
Route::post('/polis', [PoliController::class, 'store']);
Route::get('/logs', function() {
    return Log::all();
});
