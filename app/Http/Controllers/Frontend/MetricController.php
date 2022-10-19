<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MetricController extends Controller
{
    /**
     * Get Total Bitcoiners
     *
     * @return JsonResponse
     */
    public function totalBitcoiners(): JsonResponse
    {
        // Check if the total bitcoiners metric is cached in Redis
        $totalBitcoiners = Redis::get('total_bitcoiners');

        if ($totalBitcoiners) {
            return response()->json([
                'totalBitcoiners' => (int)$totalBitcoiners,
            ]);
        }

        $totalBitcoiners = User::where('type', UserType::BITCOINER)->count();

        // Cache the total bitcoiners metric in Redis for 1 hour
        Redis::setex('total_bitcoiners', 3600, $totalBitcoiners);

        return response()->json([
            'totalBitcoiners' => $totalBitcoiners,
        ]);
    }

    /**
     * Get Total Shitcoiners
     *
     * @return JsonResponse
     */
    public function totalShitcoiners(): JsonResponse
    {
        // Check if the total shitcoiners metric is cached in Redis
        $totalShitcoiners = Redis::get('total_shitcoiners');

        if ($totalShitcoiners) {
            return response()->json([
                'totalShitcoiners' => (int)$totalShitcoiners,
            ]);
        }

        $totalShitcoiners = User::where('type', UserType::SHITCOINER)->count();

        // Cache the total shitcoiners metric in Redis for 1 hour
        Redis::setex('total_shitcoiners', 3600, $totalShitcoiners);

        return response()->json([
            'totalShitcoiners' => $totalShitcoiners,
        ]);
    }

    /**
     * Get Total Nocoiners
     *
     * @return JsonResponse
     */
    public function totalNocoiners(): JsonResponse
    {
        // Check if the total nocoiners metric is cached in Redis
        $totalNocoiners = Redis::get('total_nocoiners');

        if ($totalNocoiners) {
            return response()->json([
                'totalNocoiners' => (int)$totalNocoiners,
            ]);
        }

        $totalNocoiners = User::where('type', UserType::NOCOINER)->count();

        // Cache the total nocoiners metric in Redis for 1 hour
        Redis::setex('total_nocoiners', 3600, $totalNocoiners);

        return response()->json([
            'totalNocoiners' => $totalNocoiners,
        ]);
    }
}
