<?php

namespace App\Http\Controllers;

use App\Models\DepositRequest;
use App\Models\ActivityLog;
use App\Http\Requests\StoreDepositRequestRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class DepositRequestController extends Controller
{
    use AuthorizesRequests;

    // Fonction helper pour enregistrer une activité
    private function logActivity($depositRequest, $action, $details = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_table' => 'deposit_requests',
            'target_id' => $depositRequest->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

public function index(): JsonResponse
    {
        $this->authorize('viewAny' , DepositRequest::class);
        $requests = DepositRequest::with('applicant', 'assignedManager', 'referenceBrouillon.category')
            ->latest()
            ->get();

        return response()->json([
            'deposit_requests' => $requests,
            'pending_count'    => $requests->where('status', 'pending')
            ->whereNull('assigned_manager_id')
            ->count(),
        ], 200);
    }

    //Pour la création de la demande
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

        $this->logActivity($depositRequest, 'Demande déposée');
        
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

//Pour  assigner la demande a un responsable
    public function assign(Request $request, int $id): JsonResponse
{
    $depositRequest = DepositRequest::findOrFail($id);
      $this->authorize('assign', $depositRequest);

    $request->validate([
        'manager_id' => ['required', 'exists:users,id'],
    ]);

    $oldManager = $depositRequest->assignedManager;
    $depositRequest->update([
        'assigned_manager_id' => $request->manager_id,
    ]);

    $this->logActivity($depositRequest, $oldManager ? 'Demande réaffectée' : 'Demande affectée');

    return response()->json([
        'message'         => 'Demande affectée avec succès.',
        'deposit_request' => $depositRequest->load('applicant', 'assignedManager'),
    ]);
}


//Pour les assignation des responsable demande
public function myAssignedRequests(): JsonResponse
{
    $requests = DepositRequest::with('applicant' , 'referenceBrouillon.category', 'activityLogs.user')
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
    
    $requests = DepositRequest::with('assignedManager' ,'referenceBrouillon.category', 'activityLogs.user')
        ->where('applicant_id', Auth::id())
        ->latest()
        ->get();

    return response()->json([
        'deposit_requests' => $requests,
    ]);
}

// Pour valider une demande
public function approve(int $id): JsonResponse
{
    $depositRequest = DepositRequest::findOrFail($id);
    
    $depositRequest->update([
        'status' => 'approved_by_manager',
    ]);

    $this->logActivity($depositRequest, 'Demande approuvée par responsable');

    return response()->json([
        'message' => 'Demande validée avec succès.',
        'deposit_request' => $depositRequest->load('applicant', 'assignedManager'),
    ]);
}

// Pour rejeter une demande
public function reject(Request $request, int $id): JsonResponse
{
    $depositRequest = DepositRequest::findOrFail($id);
    
    $request->validate([
        'justification' => ['required', 'string'],
    ]);
    
    $depositRequest->update([
        'status' => 'rejected_by_manager',
        'justification' => $request->justification,
    ]);

    $this->logActivity($depositRequest, 'Demande rejetée par responsable');

    return response()->json([
        'message' => 'Demande rejetée .',
        'deposit_request' => $depositRequest->load('applicant', 'assignedManager'),
    ]);
}

//Pour la publication de la reference 

public function publish(int $id): JsonResponse
{
    $depositRequest = DepositRequest::with('referenceBrouillon.category')->findOrFail($id);

    if ($depositRequest->status !== 'approved_by_manager') {
        return response()->json(['message' => 'La demande doit être validée par un responsable avant publication.'], 422);
    }

    $brouillon = $depositRequest->referenceBrouillon;
    if (!$brouillon) {
        return response()->json(['message' => 'Aucun brouillon de référence trouvé.'], 422);
    }

    // Créer la référence publiée depuis le brouillon
    $reference = \App\Models\Reference::create([
        'title'            => $brouillon->title,
        'subtitle'         => $brouillon->subtitle,
        'abstract'         => $brouillon->abstract,
        'isbn'             => $brouillon->isbn,
        'publication_year' => $brouillon->publication_year,
        'language'         => $brouillon->language,
        'document_type'    => $brouillon->document_type,
        'category_id'      => $brouillon->category_id,
        'publisher_id'     => 1, //à lier au brouillon si nécessaire
        'uploaded_by'      => $depositRequest->applicant_id,
        'cover_image'      => $brouillon->cover_image,
        'file_path'        => $brouillon->file_path,
        'pages'            => $brouillon->pages,
        'status'           => 'published',
    ]);

    $depositRequest->update(['status' => 'published']);
    $this->logActivity($depositRequest, 'Référence publiée');

    return response()->json([
        'message'   => 'Référence publiée avec succès.',
        'reference' => $reference,
    ]);
}


}
