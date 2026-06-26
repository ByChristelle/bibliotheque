<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CategorieController extends Controller
{
 
    public function index(){
        $categories= Category::with('references')->latest()->get();

        return response()->json([
            'categories'=>$categories,
        ],200);
    }

       // Créer une nouvelle catégorie
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'category' => $category
        ], 201);
    }

    //Pour la modification 
    public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255|unique:categories,name,' . $id,
        'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
        'description' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $category->update($request->only('name', 'slug', 'description'));

    return response()->json([
        'message' => 'Catégorie modifiée avec succès',
        'category' => $category
    ]);
}

}
