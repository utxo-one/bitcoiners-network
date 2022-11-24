<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\FollowType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Repositories\LeaderboardRepository;
use App\Repositories\TweetRepository;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
        private TweetRepository $tweetRepository
    ) {
    }

    public function users(
        string $userType = UserType::BITCOINER,
        string $followType = FollowType::FOLLOWER,
        string $followUserType = UserType::BITCOINER,
        string $minFollowers = '1000',
        string $maxFollowers = '5000000',
    ): JsonResponse {

        $leaderboard = $this->leaderboardRepository->getLeaderboard(
            minFollowers: (int) $minFollowers,
            maxFollowers: (int) $maxFollowers,
            userType: UserType::fromValue($userType),
            followType: FollowType::fromValue($followType),
            followUserType: UserType::fromValue($followUserType),
        );

        return response()->json($leaderboard);
    }

    public function tweets(
        string $userType = UserType::BITCOINER,
        string $orderBy = 'likes',
        string $timeframe = '0',
        string $order = 'desc',
        int $minLikes = 10,
        int $minReplies = 0,
        int $minRetweets = 0,
        int $limit = 100,
    ): JsonResponse {

        $tweets = $this->tweetRepository->getTweets(
            minLikes: $minLikes,
            minReplies: $minReplies,
            minRetweets: $minRetweets,
            timeframe: (int) $timeframe,
            userType: UserType::fromValue($userType),
            orderBy: $orderBy,
            order: $order,
            limit: $limit,
        );

        return response()->json($tweets);
    }
}
