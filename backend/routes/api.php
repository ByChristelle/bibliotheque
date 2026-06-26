<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\UserController; 

// ── 1. Routes Publiques (Visiteurs) ──────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ── 2. Routes Privées (Utilisateurs connectés via Session/Cookie) ────
Route::middleware('auth:sanctum')->group(function () {
    
    // Déconnexion propre
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route standard /me reliée à ton AuthController
    Route::get('/me', [AuthController::class, 'me']); 

    // Gestion des utilisateurs
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    //pour archiver les users
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
    
    //Gestion des categories 
    Route::get('/categories' , [CategorieController::class , 'index']);
    Route::post('/categories' , [CategorieController::class , 'store']);
    Route::put('/categories/{id}', [CategorieController::class, 'update']);
    Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);


        //Gestion des references 
         Route::get('/references' , [ReferenceController::class , 'index']);
        Route::post('/references' , [ReferenceController::class , 'store']);

});


// ── 3. Route de test ──────────────────────────────────────────────────
Route::get('/test', fn () => response()->json([
    'status'  => true,
    'message' => 'API is working correctly 🚀',
]));