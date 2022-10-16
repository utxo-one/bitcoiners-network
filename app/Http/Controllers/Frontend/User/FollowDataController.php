<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FollowDataController extends Controller
{
    public function show(string $username)
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        return response()->json([
            'following_data' => $user->following_data,
            'follower_data' => $user->follower_data,
        ], Response::HTTP_OK);
    }
}
