<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use App\Models\User;
use App\Models\Category;
use App\Models\DepositRequest;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'references'   => Reference::where('status', 'published')->count(),
            'users'        => User::count(),
            'categories'   => Category::count(),
            'downloads'    => Reference::sum('download_count'),
            'pending'      => DepositRequest::where('status', 'pending')->whereNull('assigned_manager_id')->count(),
            'affectations' => DepositRequest::whereNotNull('assigned_manager_id')->whereNotIn('status', ['published', 'rejected'])->count(),
            'published'    => DepositRequest::where('status', 'published')->count(),
            'total_requests' => DepositRequest::count(),
        ];

        return response()->json(['stats' => $stats]);
    }
}
