<?php

namespace App\Repositories;

use App\Enums\UserType;
use App\Models\Tweet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TweetRepository
{
    private int $limit = 100;
    private int $cacheTime = 60 * 60 * 24;

    public function getTimeline(User $user): Collection
    {
        return Cache::remember("timeline:{$user->twitter_id}", $this->cacheTime, function () use ($user) {
            return Tweet::where('user_id', $user->twitter_id)
                ->orderBy('created_at', 'desc')
                ->limit($this->limit)
                ->get();
        });
    }

    public function getTimelineByRetweets(User $user): Collection
    {
        return Cache::remember("timeline:{$user->twitter_id}:retweets", $this->cacheTime, function () use ($user) {
            return Tweet::where('user_id', $user->twitter_id)
                ->where('retweets', '>', 0)
                ->orderBy('retweets', 'desc')
                ->limit($this->limit)
                ->get();
        });
    }

    public function getTimelineByLikes(User $user): Collection
    {
        return Cache::remember("timeline:{$user->twitter_id}:likes", $this->cacheTime, function () use ($user) {
            return Tweet::where('user_id', $user->twitter_id)
                ->where('likes', '>', 0)
                ->orderBy('likes', 'desc')
                ->limit($this->limit)
                ->get();
        });
    }

    public function getTimelineByReplies(User $user): Collection
    {
        return Cache::remember("timeline:{$user->twitter_id}:replies", $this->cacheTime, function () use ($user) {
            return Tweet::where('user_id', $user->twitter_id)
                ->where('replies', '>', 0)
                ->orderBy('replies', 'desc')
                ->limit($this->limit)
                ->get();
        });
    }

    public function getTweets(
        int $minLikes = 10,
        int $minReplies = 0,
        int $minRetweets = 0,
        int $timeframe = 0,
        UserType $userType = UserType::BITCOINER,
        string $orderBy = 'likes',
        string $order = 'desc',
        int $limit = 100,
    ): Collection {

        // If the userType is not one of the UserType Enums, throw exception
        if (!in_array($userType, UserType::cases())) {
            throw new \Exception('Invalid user type');
        }

        return Cache::remember(
            "tweets:{$userType->value}:{$orderBy}:{$order}:{$limit}:{$minLikes}:{$minReplies}:{$minRetweets}:{$timeframe}",
            $this->cacheTime,
            function () use (
                $minLikes,
                $minReplies,
                $minRetweets,
                $timeframe,
                $userType,
                $orderBy,
                $order,
                $limit,
            ) {

                $tweets = Tweet::query()
                    ->whereHas('user', fn ($query) => $query->where('type', '=', $userType))
                    ->with('user')
                    ->where('likes', '>=', $minLikes)
                    ->where('replies', '>=', $minReplies)
                    ->where('retweets', '>=', $minRetweets)
                    ->where('in_reply_to_user_id', '=', null)
                    ->orderBy($orderBy, $order)
                    ->limit($limit);

                if ($timeframe !== 0) {
                    $tweets->where('created_at', '>=', Carbon::now()->subDays($timeframe));
                }

                return $tweets->get();
            }
        );
    }
}
