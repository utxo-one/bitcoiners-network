<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use App\Models\User;
use App\Repositories\SearchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    public function __construct(private SearchRepository $searchRepository)
    {
    }

    public function autoFill(): JsonResponse
    {
        try {
            return response()->json($this->searchRepository->autoFill(request('q')));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function search(): JsonResponse
    {
        try {
            return response()->json($this->searchRepository->search(request('q')));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
