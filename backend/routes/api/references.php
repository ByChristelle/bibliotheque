<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReferenceController;

        //Gestion des references 

        Route::middleware(['auth:sanctum' ])->group(function(){
                Route::get('/references' , [ReferenceController::class , 'index']);
        Route::post('/references' , [ReferenceController::class , 'store'])->middleware('admin');
});
