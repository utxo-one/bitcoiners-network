<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FollowService;
use Illuminate\Console\Command;

class CreateFollowRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:follow-requests';

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
        $amount = $this->ask('How many follow requests do you want to create?');
        $username = $this->ask('What is your username?');

        $user = User::where('twitter_username', $username)->first();

        $this->info('Creating follow requests...');
        $followService = new FollowService();
        $followService->createFollowRequests($user, $amount);
        
        $this->info('Follow requests created successfully!');
        return 0;
    }
}
