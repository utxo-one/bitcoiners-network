<?php

namespace App\Console\Commands;

use App\Enums\FollowType;
use App\Enums\UserType;
use App\Repositories\LeaderboardRepository;
use Illuminate\Console\Command;

class BuildLeaderboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:build-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('building bitcoinersByBitcoinerFollowers cache...');

        $leaderboardRepository = new LeaderboardRepository();

        $this->info('building bitcoiners by bitcoiner followers cache...');
        $leaderboardRepository->getLeaderboard(
            minFollowers: 100,
            maxFollowers: 10000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWER,
            followUserType: UserType::BITCOINER,
        );

        $this->alert('bitcoiners by bitcoiner followers cache built');

        $this->info('building bitcoiners by bitcoiner following cache...');
        $leaderboardRepository->getLeaderboard(
            minFollowers: 100,
            maxFollowers: 10000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWING,
            followUserType: UserType::BITCOINER,
        );

        $this->alert('bitcoiners by bitcoiner following cache built');

        $this->info('building bitcoiners by shitcoiner followers cache...');
        $leaderboardRepository->getLeaderboard(
            minFollowers: 100,
            maxFollowers: 10000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWER,
            followUserType: UserType::SHITCOINER,
        );

        $this->alert('bitcoiners by shitcoiner following cache built');

        $this->info('building bitcoiners by shitcoiner following cache...');
        $leaderboardRepository->getLeaderboard(
            minFollowers: 100,
            maxFollowers: 10000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWING,
            followUserType: UserType::SHITCOINER,
        );

        $this->alert('bitcoiners by shitcoiner following cache built');

        $this->info('building bitcoiner by nocoiner followers cache...');
        $leaderboardRepository->getLeaderboard(
            minFollowers: 100,
            maxFollowers: 10000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWER,
            followUserType: UserType::NOCOINER,
        );

        $this->alert('bitcoiner by nocoiner followers cache built');

        $this->info('building bitcoiner by nocoiner following cache...');
        $leaderboardRepository->getLeaderboard(
            minFollowers: 100,
            maxFollowers: 10000000,
            userType: UserType::BITCOINER,
            followType: FollowType::FOLLOWING,
            followUserType: UserType::NOCOINER,
        );

        $this->alert('bitcoiner by nocoiner following cache built');


        return 0;
        return 0;
    }
}
