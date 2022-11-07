<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function autoFill(): JsonResponse
    {
        return response()->json(
            User::query()
            ->where('twitter_username', 'like', '%' . request('q') . '%')
            ->orderBy('twitter_count_followers', 'desc')
            ->get(['twitter_username', 'name', 'twitter_id'])
        );
    }

    public function search(): JsonResponse
    {
        // Try to find an exact match user
        $user = User::where('twitter_username', request('q'))->first();

        // If no exact match, find 5 users with a similar name
        if (!$user) {
            $user = User::query()
                ->where('twitter_username', 'like', '%' . request('q') . '%')
                ->orderBy('twitter_count_followers', 'desc')
                ->take(5)
                ->get();
        }

        // Find some tweets that match this query
        $tweets = Tweet::query()
            ->where('text', 'like', '%' . request('q') . '%')
            ->with('user')
            ->orderBy('likes', 'desc')
            ->take(10)
            ->get();

        // Return the results
        return response()->json([
            'users' => $user,
            'tweets' => $tweets,
        ]);
    }
}
