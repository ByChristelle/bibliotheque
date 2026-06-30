<?php

namespace App\Http\Controllers;

use App\Models\DepositRequest;
use App\Http\Requests\StoreDepositRequestRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class DepositRequestController extends Controller
{
    use AuthorizesRequests;


public function index(): JsonResponse
{
    $this->authorize('viewAny' , DepositRequest::class);
    $requests = DepositRequest::with('applicant')
        ->latest()
        ->get();

    return response()->json([
        'deposit_requests' => $requests,
        'pending_count'    => $requests->where('status', 'pending')
        ->whereNull('assigned_manager_id')
        ->count(),
    ], 200);
}

    //Pour la création 
    public function store(StoreDepositRequestRequest $request): JsonResponse
    {
    $this->authorize('create' , DepositRequest::class);

        $data = $request->validated();

        if ($request->hasFile('proposed_file')) {
            $data['proposed_file'] = $request->file('proposed_file')
                ->store('deposit_files', 'public');
        }

        $depositRequest = DepositRequest::create([
            'applicant_id'  => Auth::id(),
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'proposed_file' => $data['proposed_file'] ?? null,
            'status'        => 'pending',
        ]);

        
        // 2. Créer le brouillon de référence associé
        $referenceBrouillonData = [
            'deposit_request_id' => $depositRequest->id,
            'title'              => $data['title'],
            'subtitle'           => $data['subtitle'] ?? null,
            'authors'            => $data['authors'],
            'category_id'        => $data['category_id'],
            'publisher'          => $data['publisher'] ?? null,
            'publication_year'   => $data['publication_year'] ?? null,
            'pages'              => $data['pages'] ?? null,
            'isbn'               => $data['isbn'] ?? null,
            'language'           => $data['language'],
            'document_type'      => $data['document_type'],
            'keywords'           => $data['keywords'] ?? null,
            'abstract'           => $data['abstract'] ?? null,
        ];

        // Uploader l'image de couverture si présente
        if (isset($data['cover_image'])) {
            $referenceBrouillonData['cover_image'] = $data['cover_image']->store('reference_covers', 'public');
        }

        // Uploader le fichier PDF si présent
        if (isset($data['file'])) {
            $referenceBrouillonData['file_path'] = $data['file']->store('reference_files', 'public');
        } elseif (isset($data['proposed_file'])) {
            $referenceBrouillonData['file_path'] = $depositRequest->proposed_file;
        }

        $depositRequest->referenceBrouillon()->create($referenceBrouillonData);

        return response()->json([
            'message'        => 'Demande soumise avec succès.',
            'deposit_request' => $depositRequest->load('applicant'),
        ], 201);
    }


    public function assign(Request $request, int $id): JsonResponse
{
    $depositRequest = DepositRequest::findOrFail($id);
      $this->authorize('assign', $depositRequest);

    $request->validate([
        'manager_id' => ['required', 'exists:users,id'],
    ]);

    $depositRequest->update([
        'assigned_manager_id' => $request->manager_id,
    ]);

    return response()->json([
        'message'         => 'Demande affectée avec succès.',
        'deposit_request' => $depositRequest->load('applicant', 'assignedManager'),
    ]);
}


//Pour les assignation des responsable demande
public function myAssignedRequests(): JsonResponse
{
    $requests = DepositRequest::with('applicant')
        ->where('assigned_manager_id', Auth::id())
        ->latest()
        ->get();

    return response()->json([
        'deposit_requests' => $requests,
    ]);
}

//Pour les demandes de depot de l'utilisateur
public function myRequests(){
    $this->authorize('viewAny', DepositRequest::class); 
    
    $requests = DepositRequest::with('assignedManager')
        ->where('applicant_id', Auth::id())
        ->latest()
        ->get();

    return response()->json([
        'deposit_requests' => $requests,
    ]);
}

}
