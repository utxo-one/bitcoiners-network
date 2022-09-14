<?php

namespace App\Http\Controllers\Frontend\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    /**
     * Get All Followers
     * 
     * A list of users that the authenticated user is following.
     * 
     * @group Follow
     *
     * @param UserType $userType
     * @return JsonResponse
     */
    public function index(UserType $userType): JsonResponse
    {
        $followers = auth()->user()->getFollowersByType($userType);

        return response()->json([
            'followers' => $followers->paginate(perPage: 20),
        ]);
    }
}
