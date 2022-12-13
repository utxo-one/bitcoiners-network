<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwitterUserActionRequest;
use App\Models\Block;
use App\Models\Tweet;
use App\Models\User;
use App\Services\TweetService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserActionController extends Controller
{
    public function __construct(private UserService $userService, private TweetService $tweetService)
    {
    }

    public function mute(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        try {
            return response()->json(
                $this->userService->muteUser($user),
                Response::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function unmute(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        try {
            return response()->json(
                $this->userService->unmuteUser($user),
                Response::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function block(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        try {
            return response()->json(
                $this->userService->blockUser($user),
                Response::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function unblock(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        try {
            return response()->json(
                $this->userService->unblockUser($user),
                Response::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function like(string $tweetId): JsonResponse
    {
        $tweet = Tweet::where('id', $tweetId)->firstOrFail();

        try {
            return response()->json(
                $this->tweetService->like($tweet),
                Response::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function unlike(string $tweetId): JsonResponse
    {
        $tweet = Tweet::where('id', $tweetId)->firstOrFail();

        try {
            return response()->json(
                $this->tweetService->unlike($tweet),
                Response::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function retweet(string $tweetId): JsonResponse
    {
        $tweet = Tweet::where('id', $tweetId)->firstOrFail();

        try {
            return response()->json(
                $this->tweetService->retweet($tweet),
                Response::HTTP_CREATED,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function unretweet(string $tweetId): JsonResponse
    {
        $tweet = Tweet::where('id', $tweetId)->firstOrFail();

        try {
            return response()->json(
                $this->tweetService->unretweet($tweet),
                Response::HTTP_OK,
            );
        } catch (\Exception $e) {
            return response()->json(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function isBlocked(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $block = Block::query()
            ->where('target_id', $user->twitter_id)
            ->where('user_id', auth()->user()->twitter_id)
            ->first();

        return response()->json($block ? true : false);
    }

    public function isMuted(string $username): JsonResponse
    {
        $user = User::where('twitter_username', $username)->firstOrFail();

        $mute = auth()->user()->mutes()->where('target_id', $user->twitter_id)->first();

        return response()->json($mute ? true : false);
    }
}
