<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(){
      $this->authorize('viewAny',User::class);

        $user=User::latest()->get();
        
        return response()->json([
          'users'=> $user,
        //   'message'=>'utilisateur crée avec success'
        ],200);
    }
    public function store(StoreUsersRequest $request): JsonResponse
    {
      $this->authorize('create',User::class);
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


  public function update(UpdateUserRequest $requestUpdate, int $id): JsonResponse
{
    $user = User::findOrFail($id);
    $data= $requestUpdate->validated();
    $this->authorize('update',$user);

    if (isset($data['first_name'])) $user->first_name = $data['first_name'];
    if (isset($data['last_name'])) $user->last_name = $data['last_name'];
    if (isset($data['email'])) $user->email = $data['email'];
    if (isset($data['phone'])) $user->phone = $data['phone'];
    if (isset($data['role'])) $user->role = $data['role'];
    if (!empty($data['password'])) $user->password = Hash::make($data['password']);
    if (isset($data['status'])) $user->status = $data['status'];

    $user->save();

    return response()->json([
        'user' => $user,
        'message' => 'Utilisateur modifié avec succès !'
    ], 200);
}
    public function destroy( int $id)
{
    $user = User::findOrFail($id);
          $this->authorize('delete',$user);
    // Laravel remplit automatiquement la colonne 'deleted_at' grâce au trait SoftDeletes
    $user->delete(); 

    return response()->json([
        'message' => 'Utilisateur archivé avec succès.',
        'id' => $id // On renvoie l'ID pour aider le Front à nettoyer sa liste
    ], 200);
}
}
