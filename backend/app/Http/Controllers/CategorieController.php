<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Validator;

class CategorieController extends Controller
{
    use AuthorizesRequests;
 
    public function index(){
        $this->authorize('viewAny',Category::class);
        $categories= Category::with('references')->latest()->get();

        return response()->json([
            'categories'=>$categories,
        ],200);
    }

       // Créer une nouvelle catégorie
    public function store(StoreCategorieRequest $requestStore){

        $this->authorize('create', Category::class);
        //recuperer les donnés depuis le request
        $data=$requestStore->validated();
        
           if (!isset($data['status'])) {
        $data['status'] = 'active';
    }

        $category = Category::create($data);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'category' => $category
        ], 201);
    }

    //Pour la modification 
    public function update(UpdateCategorieRequest $request, int $id)
{
    $category = Category::findOrFail($id);
    $data= $request ->validated();
   

    $category->update($data);

    return response()->json([
        'message' => 'Catégorie modifiée avec succès',
        'category' => $category
    ]);
}


public function destroy (int $id){
    $categorie = Category::findOrFail($id);
      $this->authorize('delete', $categorie);
    
    // Vérifier si la catégorie a des références
    if ($categorie->references()->count() > 0) {
        return response()->json([
            'message' => 'Impossible de supprimer cette catégorie car elle contient des références.'
        ], 422);
    }

    $categorie->delete();

    return response()->json([
        'message' => 'Catégorie archivée avec succès.',
        'id'=>$id
    ], 200);
}

}
