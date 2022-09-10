<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use Carbon\Carbon;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;
use UtxoOne\TwitterUltimatePhp\Models\Users as TwitterUsers;

class CrawlerService
{
    public function crawlBitcoiners(?int $limit = 1): void
    {
        $userClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));

        $bitcoiner = User::query()
            ->where('type', UserType::BITCOINER)
            ->where('last_crawled_at', NULL)
            ->where('twitter_count_followers', '>', 1000)
            ->where('twitter_count_followers', '<', 8000)
            ->where('twitter_count_following', '<', 5000)
            ->inRandomOrder()
            ->first();

        $userService = new UserService();
        $twitterUser = $userClient->getUserById($bitcoiner->twitter_id);
        $userService->processTwitterUser($twitterUser);

        $bitcoiner->last_crawled_at = Carbon::now();
        $bitcoiner->save();
    }
}
