<?php

namespace App\Policies;

use App\Models\DepositRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepositRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // return in_array($user->role , ['admin','responsable_demande' ]);
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DepositRequest $depositRequest): bool
    {
       // Admin, responsable_demande affecté, ou l'applicant lui-même
        return $user->role === 'admin' || 
               $user->id === $depositRequest->applicant_id || 
               $user->id === $depositRequest->assigned_manager_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role==='user';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DepositRequest $depositRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DepositRequest $depositRequest): bool
    {
       return $user->role === 'admin';
    }

      // Nouvelle méthode pour l'assignation
    public function assign(User $user, DepositRequest $depositRequest): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DepositRequest $depositRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DepositRequest $depositRequest): bool
    {
        return false;
    }
}
