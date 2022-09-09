<?php

namespace Tests\Unit\Services\Web\Auth;

use App\Enums\UserType;
use App\Services\Web\Auth\TwitterAuthService;
use Tests\TestCase;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class TwitterAuthServiceTest extends TestCase
{
    private TwitterAuthService $twitterAuthService;
    private string $bearerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->twitterAuthService = new TwitterAuthService();
        $this->bearerToken = env('TWITTER_BEARER_TOKEN');
    }

    /** @group classifyUser */
    public function testClassifyUser(): void
    {
        $twitterUserClient = new UserClient(bearerToken: $this->bearerToken);
        $vitalik = $twitterUserClient->getUserByUsername('postmilhodl');

        $userType = $this->twitterAuthService->classifyUser($vitalik->getId());

        $this->assertEquals(UserType::SHITCOINER, $userType);
    }
}
