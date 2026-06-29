<?php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Gestion des utilisateurs

Route::middleware(['auth:sanctum','role:admin,responsable_rh'])->group(function(){

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    //pour archiver les users
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});