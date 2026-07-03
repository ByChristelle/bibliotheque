<?php
use App\Http\Controllers\CategorieController;
use Illuminate\Support\Facades\Route;

//Gestion des categories 

// Route publique pour récupérer les catégories
Route::get('/categories' , [CategorieController::class , 'index']);

Route::middleware(['auth:sanctum'])->group(function(){
    // Plus besoin de la route get ici, elle est publique
});


Route::middleware(['auth:sanctum','role:admin'])->group(function(){
    Route::post('/categories' , [CategorieController::class , 'store']);
    Route::put('/categories/{id}', [CategorieController::class, 'update']);
    Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);
    });
