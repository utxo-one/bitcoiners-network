<?php

use App\Enums\UserType;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Tests\TestCase;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations and seed database
        $this->artisan('migrate:fresh --seed');

        $this->userRepository = new UserRepository();
        $this->user = User::where('twitter_username', 'utxo_one')->firstOrFail();
    }

    /** @group getFollowData */
    public function testItCanGetFollowData(): void
    {
        $followData = $this->userRepository->getFollowData($this->user);

        $this->assertIsArray($followData);
        $this->assertArrayHasKey('follower_data', $followData);
        $this->assertIsArray($followData['follower_data']);
        $this->assertArrayHasKey('bitcoiners', $followData['follower_data']);
        $this->assertIsInt($followData['follower_data']['bitcoiners']);
        $this->assertIsInt($followData['follower_data']['shitcoiners']);
        $this->assertIsInt($followData['follower_data']['nocoiners']);
        $this->assertArrayHasKey('following_data', $followData);
        $this->assertIsArray($followData['following_data']);
        $this->assertArrayHasKey('bitcoiners', $followData['following_data']);
        $this->assertIsInt($followData['following_data']['bitcoiners']);
        $this->assertIsInt($followData['following_data']['shitcoiners']);
        $this->assertIsInt($followData['following_data']['nocoiners']);
    }
}
