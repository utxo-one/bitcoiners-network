<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\TweetRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TweetController extends Controller
{
    public function __construct(private TweetRepository $tweetRepository)
    {
    }

    public function show(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $tweets = $this->tweetRepository->getTimeline($user);

        return response()->json($this->paginate($tweets));
    }
}
