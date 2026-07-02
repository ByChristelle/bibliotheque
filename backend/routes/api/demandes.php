<?php
use App\Http\Controllers\DepositRequestController;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

    //Pour les demandes de depot 
    // Route::middleware(['auth:sanctum' , 'role:user'])->group(function(){
        
    // });
    Route::post('/deposit-requests', [DepositRequestController::class, 'store'])->middleware('role:user');
    Route::get('/deposit-requests', [DepositRequestController::class, 'index'])->middleware('role:admin,responsable_demande');
    Route::put('/deposit-requests/{id}/assign', [DepositRequestController::class, 'assign'])->middleware('role:admin');
    Route::put('/deposit-requests/{id}/approve', [DepositRequestController::class, 'approve'])->middleware('role:responsable_demande');
    Route::put('/deposit-requests/{id}/reject', [DepositRequestController::class, 'reject'])->middleware('role:responsable_demande');
    Route::get('/my-assigned-requests', [DepositRequestController::class, 'myAssignedRequests'])->middleware('role:responsable_demande');
    Route::get('/my-requests', [DepositRequestController::class , 'myRequests'])->middleware('role:user') ;

    Route::put('/deposit-requests/{id}/approve',[DepositRequestController::class, 'approve'])->middleware('role:responsable_demande');
Route::put('/deposit-requests/{id}/reject',  [DepositRequestController::class, 'reject'])->middleware('role:responsable_demande');
