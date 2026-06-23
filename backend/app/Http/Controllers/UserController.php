<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUsersRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index(){
        $user=User::latest()->get();
        
        return response()->json([
          'users'=> $user,
        //   'message'=>'utilisateur crée avec success'
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
            'user' => $user,
            'message' => 'Utilisateur créé avec succès.'
        ], 201);
    }


  public function update(Request $request, int $id): JsonResponse
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'first_name' => ['nullable', 'string', 'max:255'],
        'last_name'  => ['nullable', 'string', 'max:255'],
        'email'      => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'phone'      => ['nullable', 'string', 'max:20'],
        'role'       => ['nullable', 'string', 'in:admin,responsable_rh,responsable_demande,user'],
        'password'   => ['nullable', 'string', 'min:8'],
        'status'     => ['nullable', 'string', 'in:active,inactive,suspended'],
    ]);

    if (isset($validated['first_name'])) $user->first_name = $validated['first_name'];
    if (isset($validated['last_name'])) $user->last_name = $validated['last_name'];
    if (isset($validated['email'])) $user->email = $validated['email'];
    if (isset($validated['phone'])) $user->phone = $validated['phone'];
    if (isset($validated['role'])) $user->role = $validated['role'];
    if (!empty($validated['password'])) $user->password = Hash::make($validated['password']);
    if (isset($validated['status'])) $user->status = $validated['status'];

    $user->save();

    return response()->json([
        'user' => $user,
        'message' => 'Utilisateur modifié avec succès !'
    ], 200);
}
    public function destroy( int $id)
{
    $user = User::findOrFail($id);
    
    // Laravel remplit automatiquement la colonne 'deleted_at' grâce au trait SoftDeletes
    $user->delete(); 

    return response()->json([
        'message' => 'Utilisateur archivé avec succès.',
        'id' => $id // On renvoie l'ID pour aider le Front à nettoyer sa liste
    ], 200);
}
}
