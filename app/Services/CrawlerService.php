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
            ->inRandomOrder()
            ->first();

        $userService = new UserService();

        $twitterUser = $userClient->getUserById($bitcoiner->twitter_id);
        $userService->processTwitterUser($twitterUser);
    }
}
