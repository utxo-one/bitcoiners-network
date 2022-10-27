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

    public function getBitcoinerMostRetweetedToday(): Collection
    {
        return Cache::remember(
            'most_retweeted_this_week',
            $this->cacheTime - ($this->cacheTime / 4),
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subDay())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('retweets', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRetweetedThisWeek(): Collection
    {
        return Cache::remember(
            'most_retweeted_this_week',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subWeek())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('retweets', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRetweetedThisMonth(): Collection
    {
        return Cache::remember(
            'most_retweeted_this_month',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subMonth())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('retweets', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRetweetedThisYear(): Collection
    {
        return Cache::remember(
            'most_retweeted_this_year',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subYear())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('retweets', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRetweetedAllTime(): Collection
    {
        return Cache::remember(
            'most_retweeted_all_time',
            $this->cacheTime,
            fn () => Tweet::orderBy('retweets', 'desc')
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('retweets', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostLikedToday(): Collection
    {
        return Cache::remember(
            'most_liked_today',
            $this->cacheTime - ($this->cacheTime / 4),
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subDay())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('likes', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostLikedThisWeek(): Collection
    {
        return Cache::remember(
            'most_liked_this_week',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subWeek())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('likes', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostLikedThisMonth(): Collection
    {
        return Cache::remember(
            'most_liked_this_month',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subMonth())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('likes', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostLikedThisYear(): Collection
    {
        return Cache::remember(
            'most_liked_this_year',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subYear())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('likes', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostLikedAllTime(): Collection
    {
        return Cache::remember(
            'most_liked_all_time',
            $this->cacheTime,
            fn () => Tweet::orderBy('likes', 'desc')
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('likes', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRepliesToday(): Collection
    {
        return Cache::remember(
            'most_replied_today',
            $this->cacheTime - ($this->cacheTime / 4),
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subDay())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('replies', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRepliesThisWeek(): Collection
    {
        return Cache::remember(
            'most_replied_this_week',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subWeek())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('replies', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRepliesThisMonth(): Collection
    {
        return Cache::remember(
            'most_replied_this_month',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subMonth())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('replies', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRepliesThisYear(): Collection
    {
        return Cache::remember(
            'most_replied_this_year',
            $this->cacheTime,
            fn () => Tweet::where('created_at', '>=', Carbon::now()->subYear())
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('replies', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

    public function getBitcoinerMostRepliesAllTime(): Collection
    {
        return Cache::remember(
            'most_replied_all_time',
            $this->cacheTime,
            fn () => Tweet::orderBy('replies', 'desc')
                ->where('likes', '>', 0)
                ->where('replies', '>', 0)
                ->whereHas('user', fn ($query) => $query->where('type', '=', UserType::BITCOINER))
                ->with('user')
                ->orderBy('replies', 'desc')
                ->limit($this->limit)
                ->get()
        );
    }

}