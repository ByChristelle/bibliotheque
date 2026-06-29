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

}
