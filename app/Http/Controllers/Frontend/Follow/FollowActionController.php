<?php

namespace App\Http\Controllers\Frontend\Follow;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FollowActionController extends Controller
{
    public function __construct(private FollowService $followService)
    {
    }

    public function follow(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        return response()->json(
            $this->followService->followUser($user),
            Response::HTTP_CREATED,
        );
    }

    public function unfollow(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        return response()->json(
            $this->followService->unfollowUser($user),
            Response::HTTP_OK,
        );
    }
}
