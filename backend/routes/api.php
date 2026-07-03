<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatsController;

// ── 1. Routes Publiques (Visiteurs) ──────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/stats', [StatsController::class, 'index']);

// Charger les fichiers API qui ont des routes publiques ET protégées
require __DIR__ . '/api/categories.php';
require __DIR__ . '/api/references.php';


// ── 2. Routes Privées (Utilisateurs connectés via Session/Cookie) ────
Route::middleware('auth:sanctum')->group(function () {
    // Déconnexion propre
    Route::post('/logout', [AuthController::class, 'logout']);

    
    // Route standard /me reliée à ton AuthController
    Route::get('/me', [AuthController::class, 'me']); 
    
    require __DIR__ . '/api/demandes.php';
    require __DIR__ . '/api/users.php';

    // Activity Logs routes
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index']);
    Route::get('/activity-logs/deposit-request/{id}', [App\Http\Controllers\ActivityLogController::class, 'show']);
});
