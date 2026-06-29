<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ── 1. Routes Publiques (Visiteurs) ──────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ── 2. Routes Privées (Utilisateurs connectés via Session/Cookie) ────
Route::middleware('auth:sanctum')->group(function () {
    // Déconnexion propre
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route standard /me reliée à ton AuthController
    Route::get('/me', [AuthController::class, 'me']); 
    
    require __DIR__ . '/api/demandes.php';
    require __DIR__ . '/api/users.php';
    require __DIR__ . '/api/categories.php';
    require __DIR__ . '/api/references.php';
});



