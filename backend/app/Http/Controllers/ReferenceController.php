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


    public function download(int $id): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
{
    $reference = Reference::findOrFail($id);

    if (!$reference->file_path) {
        return response()->json(['message' => 'Aucun fichier disponible.'], 404);
    }

    // Incrémenter le compteur
    $reference->increment('download_count');

    $filePath = storage_path('app/public/' . $reference->file_path);

    if (!file_exists($filePath)) {
        return response()->json(['message' => 'Fichier introuvable.'], 404);
    }

    return response()->download($filePath, $reference->title . '.pdf');
}



// Pour la fonction LIRE : Affiche le fichier dans le navigateur
public function lire($id)
{
    $reference = Reference::findOrFail($id);
    $path = storage_path('app/public/' . $reference->file_path); // Adaptez le chemin selon votre stockage

    // La méthode file() configure automatiquement le Content-Disposition sur 'inline'
    return response()->file($path, [
        'Content-Type' => 'application/pdf', // Force le navigateur à l'interpréter comme un PDF
    ]);
}

}
