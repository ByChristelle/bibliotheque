<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route publique pour l'inscription, accessible uniquement par les invités (middleware guest)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
    

// ── Route de test ─────────────────────────────────────────────────────────────
Route::get('/test', fn () => response()->json([
    'status'  => true,
    'message' => 'API is working correctly 🚀',
]));
