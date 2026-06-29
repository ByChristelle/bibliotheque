<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ReferenceController extends Controller
{
        use AuthorizesRequests;

    public function index(){
        $this->authorize('viewAny' , Reference::class);
       $references = Reference::with(['category', 'publisher', 'authors', 'uploadedBy'])->latest()->get();

        return response()->json([
            'references'=>$references,
        ],200);
    }
}
