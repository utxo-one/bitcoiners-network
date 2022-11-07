<?php

use App\Enums\UserType;
use App\Services\UserService;
use Tests\TestCase;
use UtxoOne\TwitterUltimatePhp\Clients\TweetClient;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class TweetServiceTest extends TestCase
{
    private UserService $userService;
    private string $bearerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = new UserService();
        $this->bearerToken = env('TWITTER_BEARER_TOKEN');
    }

    /** @group getTimeline */
    public function testGetTimeline(): void
    {
        $tweetClient = new TweetClient(bearerToken: $this->bearerToken);

        $tweets = $tweetClient->getTweet(
            '1589608096519057409'
        );

        dd($tweets->getIncludes());
    }
}
