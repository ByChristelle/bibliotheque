<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $totalDownloads = Reference::sum('download_count');

        $stats = [
            'references' => Reference::count(),
            'users' => User::count(),
            'categories' => Category::count(),
            'downloads' => $totalDownloads,
        ];

        return response()->json([
            'stats' => $stats
        ]);
    }
}
