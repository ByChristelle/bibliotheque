<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
 
    public function index(){
        $categories= Category::with(['references'])->latest()->get();

        return response()->json([
            'categories'=>$categories,
        ],200);
    }

    public function store(){
        
    }
}
