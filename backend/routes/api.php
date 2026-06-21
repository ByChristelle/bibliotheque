<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; // <-- IL MANQUAIT CETTE LIGNE ICI !

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route publique pour l'inscription, accessible uniquement par les invités (middleware guest)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes accessibles uniquement aux utilisateurs connectés
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Permet au Frontend de récupérer l'utilisateur connecté via le cookie
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return response()->json(['user' => $request->user()]);
    });

    // Votre route simple, bien au chaud dans le groupe auth
    Route::post('/users', [UserController::class, 'store']);
});


// ── Route de test ─────────────────────────────────────────────────────────────
Route::get('/test', fn () => response()->json([
    'status'  => true,
    'message' => 'API is working correctly 🚀',
]));