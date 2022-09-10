<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use Carbon\Carbon;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;
use UtxoOne\TwitterUltimatePhp\Models\Users as TwitterUsers;

class CrawlerService
{
    public function crawlBitcoiners(?int $limit = 2): void
    {
        $userClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        $bitcoiners = User::query()
            ->where('type', UserType::BITCOINER)
            ->where('last_crawled_at', '<', Carbon::now()->subYear(1))
            ->take($limit)
            ->get();

        $userService = new UserService();

        foreach($bitcoiners as $bitcoiner) {
            $followers = $userClient->getFollowers($bitcoiner->twitter_id);
            $userService->processTwitterUsers($followers);
        }
    }
}