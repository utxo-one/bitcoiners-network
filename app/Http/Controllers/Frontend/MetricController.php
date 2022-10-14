<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function totalBitcoiners()
    {
        $totalBitcoiners = User::where('type', UserType::BITCOINER)->count();

        return response()->json([
            'totalBitcoiners' => $totalBitcoiners,
        ]);
    }

    public function totalShitcoiners()
    {
        $totalShitcoiners = User::where('type', UserType::SHITCOINER)->count();

        return response()->json([
            'totalShitcoiners' => $totalShitcoiners,
        ]);
    }

    public function totalNocoiners()
    {
        $totalNocoiners = User::where('type', UserType::NOCOINER)->count();

        return response()->json([
            'totalNocoiners' => $totalNocoiners,
        ]);
    }
}
