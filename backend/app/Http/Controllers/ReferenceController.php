<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReferenceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Reference::class);

        // Si l'utilisateur est connecté et autorisé, on renvoie toutes les références
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'responsable_rh', 'responsable_demande'])) {
            $references = Reference::with(['category', 'publisher', 'authors', 'uploadedBy'])
                ->latest()
                ->get();
        } else {
            // Sinon, seulement les publiées
            $references = Reference::with(['category', 'authors'])
                ->where('status', 'published')
                ->latest()
                ->get();
        }

        return response()->json([
            'references' => $references,
        ], 200);
    }

    public function show(  int $id)
    {
        $reference = Reference::with(['category', 'publisher', 'authors', 'uploadedBy'])->findOrFail($id);

        $this->authorize('view', $reference);

        // Si l'utilisateur n'est pas connecté ou pas autorisé, on vérifie que la référence est publiée
        if (!(Auth::check() && in_array(Auth::user()->role, ['admin', 'responsable_rh', 'responsable_demande']))) {
            if ($reference->status !== 'published') {
                abort(403, 'Référence non disponible');
            }
        }

        return response()->json([
            'reference' => $reference,
        ], 200);
    }
}
