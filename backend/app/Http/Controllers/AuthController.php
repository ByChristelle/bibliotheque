<?php

namespace App\Http\Controllers;

use App\Events\UserSuspended;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Traiter l'inscription d'un nouvel usager.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // 1. Récupérer les données validées
        $validated = $request->validated();

        // 2. Créer l'utilisateur en base de données
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status'=>'inactive',
        ]);

        // 3. Connecter automatiquement l'utilisateur (Création de la session Sanctum)
        Auth::login($user);

        // 4. Retourner une réponse claire au Frontend
        return response()->json([
            'message' => 'Inscription réussie et utilisateur connecté.',
            'user' => $user
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
{
    // 1. Tenter l'authentification avec les identifiants validés
    if (!Auth::attempt($request->only('email', 'password'))) {
        // Si ça échoue, on renvoie une erreur 422 claire (compatible avec Pinia)
        throw ValidationException::withMessages([
            'email' => ['Ces identifiants ne correspondent pas à nos enregistrements.'],
        ]);
    }

    // 2. Sécurité : Régénérer la session pour éviter la fixation de session
    $request->session()->regenerate();

    // 3. Récupérer l'utilisateur connecté
    $user = Auth::user();

    if($user->status === 'suspended'){
        // Déconnecter immédiatement (car Auth::attempt l'a connecté)
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Renvoyer une erreur claire
        throw ValidationException::withMessages([
            'email' => ['Votre compte a été suspendu. Veuillez contacter l\'administrateur.'],
        ]);

    }

    broadcast(new UserSuspended($user->id , 'Login'));

    // 4. Retourner l'utilisateur au Frontend
    return response()->json([
        'message' => 'Connexion réussie.',
        'user' => $user
    ], 200);
}


public function logout(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
{

    broadcast(new UserSuspended($request->user()->id , 'Logout'));
    // 1. Déconnecter l'utilisateur du garde de session
    Auth::guard('web')->logout();

    // 2. Invalider la session actuelle pour qu'elle ne soit plus réutilisable
    $request->session()->invalidate();

    // 3. Régénérer le jeton CSRF pour éviter les attaques après déconnexion
    $request->session()->regenerateToken();



    return response()->json([
        'message' => 'Déconnexion réussie.'
    ], 200);
}

public function me(\Illuminate\Http\Request $request): JsonResponse
    {
        // Récupère l'utilisateur actuellement authentifié via la session
        $user = $request->user();

        return response()->json([
            'user' => $user
        ], 200);
    }

}