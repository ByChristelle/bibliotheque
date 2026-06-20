<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        // Le rôle ('user') et le statut ('active') sont gérés par les valeurs par défaut de votre migration
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Connecter automatiquement l'utilisateur (Création de la session Sanctum)
        Auth::login($user);

        // 4. Retourner une réponse claire au Frontend
        return response()->json([
            'message' => 'Inscription réussie et utilisateur connecté.',
            'user' => $user
        ], 201);
    }
}