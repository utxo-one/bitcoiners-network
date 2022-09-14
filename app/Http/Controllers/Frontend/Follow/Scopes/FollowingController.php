<?php

namespace App\Http\Controllers\Frontend\Follow\Scopes;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    /**
     * Get All Follows
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
        $following = auth()->user()->getFollowingByType($userType);

        return response()->json([
            'following' => $following->paginate(perPage: 20),
        ]);
    }
}
