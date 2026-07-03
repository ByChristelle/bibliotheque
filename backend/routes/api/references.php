<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceController;

//Gestion des references

// Routes publiques
Route::get('/references' , [ReferenceController::class , 'index']);
Route::get('/references/{id}' , [ReferenceController::class , 'show']);
Route::get('/references/{id}/download', [ReferenceController::class, 'download']);
Route::get('/references/{id}/lire', [ReferenceController::class, 'lire']);


// Routes protégées
Route::middleware(['auth:sanctum' ])->group(function(){
    Route::post('/references' , [ReferenceController::class , 'store'])->middleware('admin');
});
