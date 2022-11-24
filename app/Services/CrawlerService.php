<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use UtxoOne\TwitterUltimatePhp\Clients\TweetClient;
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
            ->where('twitter_count_followers', '<', 1000)
            ->where('twitter_count_following', '<', 1000)
            ->inRandomOrder()
            ->first();

        $userService = new UserService();
        $twitterUser = $userClient->getUserById($bitcoiner->twitter_id);
        $userService->processTwitterUser($twitterUser);

        $bitcoiner->last_crawled_at = Carbon::now();

        $tweetClient = new TweetClient(bearerToken: config('services.twitter.bearer_token'));

        $tweets = $tweetClient->getTimeline(
            userId: $bitcoiner->twitter_id,
            maxResults: 5
        );

        $tweetService = new TweetService();
        $tweets = $tweetService->saveTweets($tweets, $bitcoiner);
        
        $bitcoiner->last_timeline_saved_at = Carbon::now();
        $bitcoiner->save();

        Log::notice('Crawled bitcoiner ' . $bitcoiner->twitter_username . ' with ' . $bitcoiner->twitter_count_followers . ' followers');
    }

    public function saveBitcoinerTweets(?int $limit = 50): void
    {
        $tweetClient = new TweetClient(bearerToken: config('services.twitter.bearer_token'));

        $bitcoiners = User::query()
            ->where('type', UserType::BITCOINER)
            ->where('last_timeline_saved_at', NULL)
            ->where('is_private', false)
            ->where('is_suspended', false)
            ->limit($limit)
            ->get();

        // If there are no bitcoiners, select users who's timeline hasn't been saved in 24 hours, and who have tweeted at least once in the last 90 days, and where is private is false
        if ($bitcoiners->count() === 0) {
            $bitcoiners = User::query()
                ->where('type', UserType::BITCOINER)
                ->where('last_timeline_saved_at', '<', Carbon::now()->subDay())
                ->where('last_tweeted_at', '>', Carbon::now()->subDays(90))
                ->where('twitter_count_followers', '>', 500)
                ->where('is_private', false)
                ->where('is_suspended', false)
                ->limit($limit)
                ->get();

            Log::notice('No bitcoiners to crawl, selecting bitcoiners who have tweeted in the last 90 days');
        }

        // Foreach bitcoiner, get their timeline, and save each tweet
        $tweetCount = 0;
        foreach ($bitcoiners as $bitcoiner) {
            $tweetCount++;
            try {
                $tweets = $tweetClient->getTimeline(
                    userId: $bitcoiner->twitter_id,
                    maxResults: 5
                );
    
                $tweetService = new TweetService();
                $tweets = $tweetService->saveTweets($tweets, $bitcoiner);
                $bitcoiner->last_timeline_saved_at = Carbon::now();
                $bitcoiner->save();
            } catch (\Exception $e) {

                // If error contains "Sorry, you are not authorized to see the user with id: [<id>].", set isPrivate to true for this user
                if (strpos($e->getMessage(), 'Sorry, you are not authorized to see the user with id: [' . $bitcoiner->twitter_id . '].') !== false) {
                    $bitcoiner->is_private = true;
                    $bitcoiner->save();

                    Log::notice('User ' . $bitcoiner->twitter_username . ' is private, setting isPrivate to true');

                } else {
                    $bitcoiner->is_suspended = true;
                    $bitcoiner->save();

                    Log::notice('User ' . $bitcoiner->twitter_username . ' is suspended, setting isSuspended to true. Error: ' . $e->getMessage());
                }
            }
        }
    }
}
