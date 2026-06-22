<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; 

// ── 1. Routes Publiques (Visiteurs) ──────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ── 2. Routes Privées (Utilisateurs connectés via Session/Cookie) ────
Route::middleware('auth')->group(function () {
    
    // Déconnexion propre
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route standard /me reliée à ton AuthController
    Route::get('/me', [AuthController::class, 'me']); 

    // Gestion des utilisateurs
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
});


// ── 3. Route de test ──────────────────────────────────────────────────
Route::get('/test', fn () => response()->json([
    'status'  => true,
    'message' => 'API is working correctly 🚀',
]));