<?php

namespace App\Console\Commands;

use App\Services\FollowService;
use Illuminate\Console\Command;

class ProcessFollowRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:follow-requests';

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
        $this->info('Processing follow requests...');
        $followService = new FollowService();
        $followRequests = $followService->processFollowRequests();
        $this->info('processed ' . $followRequests->count() . ' follow requests');
        return 0;
    }
}
