<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(20);

        return response()->json([
            'logs' => $logs->items(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'total' => $logs->total(),
        ]);
    }

    public function show(int $targetId): JsonResponse
    {
        $logs = ActivityLog::with('user')
            ->where('target_table', 'deposit_requests')
            ->where('target_id', $targetId)
            ->latest()
            ->get();

        return response()->json([
            'logs' => $logs,
        ]);
    }
}
