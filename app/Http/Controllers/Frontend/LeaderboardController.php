<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\LeaderboardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaderboardController extends Controller
{
    public function __construct(private LeaderboardRepository $leaderboardRepository)
    {
    }

    public function bitcoinersByBitcoinerFollowers(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByBitcoinerFollowers();

        return response()->json($this->paginate($results));
    }

    public function bitcoinersByBitcoinersFollowing(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByBitcoinersFollowing();

        return response()->json($this->paginate($results));
    }

    public function bitcoinersByShitcoinerFollowers(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByShitcoinerFollowers();

        return response()->json($this->paginate($results));
    }

    public function bitcoinersByShitcoinersFollowing(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByShitcoinersFollowing();

        return response()->json($this->paginate($results));
    }

    public function bitcoinersByNocoinerFollowers(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByNocoinerFollowers();

        return response()->json($this->paginate($results));
    }

    public function bitcoinersByNocoinersFollowing(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByNocoinersFollowing();

        return response()->json($this->paginate($results));
    }    

    private function paginate($items, $perPage = 100, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
