<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    public function index(){
       $references = Reference::with(['category', 'publisher', 'authors', 'uploadedBy'])->latest()->get();

        return response()->json([
            'references'=>$references,
        ],200);
    }
}
