<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des utilisateurs.
     */
    public function viewAny(User $user): bool
    {
        // Seuls admin et responsable_rh peuvent voir la liste
        return in_array($user->role, ['admin', 'responsable_rh']);
    }

    /**
     * Déterminer si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, User $model): bool
    {
        // Admin, RH, ou l'utilisateur lui-même peut voir son profil
        return $user->role === 'admin' || $user->role === 'responsable_rh' || $user->id === $model->id;
    }

    /**
     * Déterminer si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, User $model): bool
    {
        // Admin peut modifier tout le monde, RH peut modifier, ou l'utilisateur lui-même
        return $user->role === 'admin' || $user->role === 'responsable_rh' || $user->id === $model->id;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, User $model): bool
    {
        // Seul l'admin peut archiver
        return $user->role === 'admin';
    }

    // ... (restore et forceDelete peuvent rester à false si tu n'en as pas besoin)
}