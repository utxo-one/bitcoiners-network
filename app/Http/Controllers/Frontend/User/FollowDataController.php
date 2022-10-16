<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

class FollowDataController extends Controller
{
    public function show(string $username)
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $followDataCache = Redis::get("follow_data:{$user->twitter_id}");

        if ($followDataCache) {
            return response()->json(json_decode($followDataCache), Response::HTTP_OK);
        }

        $followData = [
            'following_data' => $user->following_data,
            'follower_data' => $user->follower_data,
        ];

        $cacheTime = 60;

        if ($user->twitter_count_followers > 1000) {
            $cacheTime = 600;
        }

        if ($user->twitter_count_followers > 5000) {
            $cacheTime = 1800;
        }

        if ($user->twitter_count_followers > 10000) {
            $cacheTime = 86400;
        }

        if ($user->twitter_count_followers > 50000) {
            $cacheTime = 604800;
        }

        Redis::setex("follow_data:{$user->twitter_id}", $cacheTime, json_encode($followData));

        return response()->json($followData, Response::HTTP_OK);
    }
}
