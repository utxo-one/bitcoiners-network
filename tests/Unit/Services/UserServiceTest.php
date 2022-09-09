<?php

use App\Enums\UserType;
use App\Services\UserService;
use Tests\TestCase;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private string $bearerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = new UserService();
        $this->bearerToken = env('TWITTER_BEARER_TOKEN');
    }

    /** @group classifyUser */
    public function testClassifyShitcoiner(): void
    {
        $twitterUserClient = new UserClient(bearerToken: $this->bearerToken);
        $vitalik = $twitterUserClient->getUserByUsername('VitalikButerin');

        $userType = $this->userService->classifyUser($vitalik->getId());

        $this->assertEquals(UserType::SHITCOINER, $userType);
    }

    /** @group classifyUser */
    public function testClassifyBitcoiner(): void
    {
        $twitterUserClient = new UserClient(bearerToken: $this->bearerToken);
        $elon = $twitterUserClient->getUserByUsername('utxoONE');

        $userType = $this->userService->classifyUser($elon->getId());

        $this->assertEquals(UserType::BITCOINER, $userType);
    }

    /** @group classifyUser */
    public function testClassifyNoCoiner(): void
    {
        $twitterUserClient = new UserClient(bearerToken: $this->bearerToken);
        $elon = $twitterUserClient->getUserByUsername('AOC');

        $userType = $this->userService->classifyUser($elon->getId());

        $this->assertEquals(UserType::NOCOINER, $userType);
    }
}
