<?php


use App\Models\User;
use App\Repositories\LeaderboardRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;


class LeaderboardRepositoryTest extends TestCase
{
    private LeaderboardRepository $leaderboardRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations and seed database
        $this->artisan('migrate:fresh --seed');

        // Clear cache
        $this->artisan('cache:clear');

        $this->leaderboardRepository = new LeaderboardRepository();
    }

    /** @group getBitcoinersByBitcoinerFollowers */
    public function testGetBitcoinersByBitcoinerFollowers(): void
    {
        $followData = $this->leaderboardRepository->getBitcoinersByBitcoinerFollowers();

        $this->assertInstanceOf(Collection::class, $followData);

        foreach($followData as $follow) {
            $this->assertInstanceOf(User::class, $follow['user']);
            $this->assertIsInt($follow['rank']);
            $this->assertIsInt($follow['bitcoiners_followers']);
        }
    }

    /** @group getBitcoinersByBitcoinersFollowing */
    public function testGetBitcoinersByBitcoinersFollowing(): void
    {
        $followData = $this->leaderboardRepository->getBitcoinersByBitcoinersFollowing();

        $this->assertInstanceOf(Collection::class, $followData);

        foreach($followData as $follow) {
            $this->assertInstanceOf(User::class, $follow['user']);
            $this->assertIsInt($follow['rank']);
            $this->assertIsInt($follow['bitcoiners_following']);
        }
    }

    /** @group getBitcoinersByShitcoinerFollowers */
    public function testGetBitcoinersByShitcoinerFollowers(): void
    {
        $followData = $this->leaderboardRepository->getBitcoinersByShitcoinerFollowers();

        $this->assertInstanceOf(Collection::class, $followData);

        foreach($followData as $follow) {
            $this->assertInstanceOf(User::class, $follow['user']);
            $this->assertIsInt($follow['rank']);
            $this->assertIsInt($follow['shitcoiners_followers']);
        }
    }

    /** @group getBitcoinersByShitcoinersFollowing */
    public function testGetBitcoinersByShitcoinersFollowing(): void
    {
        $followData = $this->leaderboardRepository->getBitcoinersByShitcoinersFollowing();

        $this->assertInstanceOf(Collection::class, $followData);

        foreach($followData as $follow) {
            $this->assertInstanceOf(User::class, $follow['user']);
            $this->assertIsInt($follow['rank']);
            $this->assertIsInt($follow['shitcoiners_following']);
        }
    }

    /** @group getBitcoinersByNocoinerFollowers */
    public function testGetBitcoinersByNocoinerFollowers(): void
    {
        $followData = $this->leaderboardRepository->getBitcoinersByNocoinerFollowers();

        $this->assertInstanceOf(Collection::class, $followData);

        foreach($followData as $follow) {
            $this->assertInstanceOf(User::class, $follow['user']);
            $this->assertIsInt($follow['rank']);
            $this->assertIsInt($follow['nocoiners_followers']);
        }
    }

    /** @group getBitcoinersByNocoinersFollowing */
    public function testGetBitcoinersByNocoinersFollowing(): void
    {
        $followData = $this->leaderboardRepository->getBitcoinersByNocoinersFollowing();

        $this->assertInstanceOf(Collection::class, $followData);

        foreach($followData as $follow) {
            $this->assertInstanceOf(User::class, $follow['user']);
            $this->assertIsInt($follow['rank']);
            $this->assertIsInt($follow['nocoiners_following']);
        }
    }
}
