<?php

namespace App\Console\Commands;

use App\Models\FollowChunk;
use App\Services\UserService;
use Illuminate\Console\Command;

class ProcessFollowChunks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:follow-chunks';

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
        $this->info('Processing follow chunks, please wait');

        $userService = new UserService();

        // Select the oldest FollowChunk that is not completed yet
        $followChunk = FollowChunk::whereNull('processed_at')->orderBy('id')->first();

        if ($followChunk) {
            $this->info("Processing follow chunk {$followChunk->id} for user {$followChunk->user->twitter_username}");

            $follows = $userService->processFollowChunk($followChunk);
            $this->info("Processed {$follows->count()} follows");
            $this->info('Done');

        } else {
            $this->info('No follow chunks to process');
        }

        return 0;
    }
}
