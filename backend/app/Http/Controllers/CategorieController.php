<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class CategorieController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Category::class);

        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'responsable_rh', 'responsable_demande'])) {
            $categories = Category::with('references')->latest()->get();
        } else {
            $categories = Category::latest()->get();
        }

        return response()->json([
            'categories' => $categories,
        ], 200);
    }

    public function store(StoreCategorieRequest $requestStore)
    {
        $this->authorize('create', Category::class);

        $data = $requestStore->validated();

        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        $category = Category::create($data);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'category' => $category
        ], 201);
    }

    public function update(UpdateCategorieRequest $request, int $id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $data = $request->validated();

        $category->update($data);

        return response()->json([
            'message' => 'Catégorie modifiée avec succès',
            'category' => $category
        ]);
    }

    public function destroy(int $id)
    {
        $categorie = Category::findOrFail($id);

        $this->authorize('delete', $categorie);

        if ($categorie->references()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette catégorie car elle contient des références.'
            ], 422);
        }

        $categorie->delete();

        return response()->json([
            'message' => 'Catégorie archivée avec succès.',
            'id' => $id
        ], 200);
    }
}
