<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUsersRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index(){
        $user=User::latest()->get();
        
        return response()->json([
          'users'=> $user,
        ],200);
    }
    public function store(StoreUsersRequest $request): JsonResponse
    {
        $data = $request->validated();

        // 1. Gestion de l'upload de l'avatar si présent
        if ($request->hasFile('avatar')) {
            // Stocke l'image dans storage/app/public/avatars et récupère le chemin
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // 2. Hachage du mot de passe pour la sécurité
        $data['password'] = Hash::make($data['password']);
        
        // 3. Statut par défaut à la création
        $data['status'] = 'active';

        // 4. Création de l'utilisateur en BDD
        $user = User::create($data);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user' => $user
        ], 201);
    }
}
