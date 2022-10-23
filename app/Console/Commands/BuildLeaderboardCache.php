<?php

namespace App\Console\Commands;

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

        $start = microtime(true);
        $leaderboardRepository = new LeaderboardRepository();
        $leaderboardRepository->getBitcoinersByBitcoinerFollowers();

        $end = microtime(true);
        $this->alert('bitcoinersByBitcoinerFollowers cache built in ' . ($end - $start) . ' seconds');

        $this->info('building bitcoinersByBitcoinersFollowing cache...');
        $start = microtime(true);
        $leaderboardRepository->getBitcoinersByBitcoinersFollowing();
        $end = microtime(true);
        $this->alert('bitcoinersByBitcoinersFollowing cache built in ' . ($end - $start) . ' seconds');

        $this->info('building bitcoinersByShitcoinerFollowers cache...');
        $start = microtime(true);
        $leaderboardRepository->getBitcoinersByShitcoinerFollowers();
        $end = microtime(true);
        $this->alert('bitcoinersByShitcoinerFollowers cache built in ' . ($end - $start) . ' seconds');

        $this->info('building bitcoinersByShitcoinersFollowing cache...');
        $start = microtime(true);
        $leaderboardRepository->getBitcoinersByShitcoinersFollowing();
        $end = microtime(true);
        $this->alert('bitcoinersByShitcoinersFollowing cache built in ' . ($end - $start) . ' seconds');

        $this->info('building bitcoinersByNocoinerFollowers cache...');
        $start = microtime(true);
        $leaderboardRepository->getBitcoinersByNocoinerFollowers();
        $end = microtime(true);
        $this->alert('bitcoinersByNocoinerFollowers cache built in ' . ($end - $start) . ' seconds');

        $this->info('building bitcoinersByNocoinersFollowing cache...');
        $start = microtime(true);
        $leaderboardRepository->getBitcoinersByNocoinersFollowing();
        $end = microtime(true);
        $this->alert('bitcoinersByNocoinersFollowing cache built in ' . ($end - $start) . ' seconds');

        return 0;
    }
}
