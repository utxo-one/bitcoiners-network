<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClassificationVoteController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        return response()->json($user->getClassificationSummary(), Response::HTTP_OK);
    }

    public function auth(): JsonResponse
    {
        return response()->json(auth()->user()->getClassificationSummary(), Response::HTTP_OK);
    }

    public function store(string $username, string $type): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $this->userService->voteClassifiction($user, $type);

        return response()->json($user->getClassificationSummary(), Response::HTTP_OK);
    }

    public function destroy(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $this->userService->unvoteClassifiction($user);

        return response()->json($user->getClassificationSummary(), Response::HTTP_OK);
    }
}
