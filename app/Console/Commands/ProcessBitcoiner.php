<?php

namespace App\Console\Commands;

use App\Services\CrawlerService;
use App\Services\UserService;
use Illuminate\Console\Command;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class ProcessBitcoiner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:bitcoiner';

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
        $twitterUsername = $this->ask('What is the Twitter username?');

        $this->info('Processing bitcoiner, please wait');

        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
        $twitterUser = $twitterUserClient->getUserByUsername($twitterUsername);
        $userService = new UserService();
        $user = $userService->processTwitterUser($twitterUser);

        $followChunkCount = $user->followChunks()->count();

        $this->info("User {$user->name} has {$followChunkCount} follow chunks");
        $this->info('Done');

        return 0;
    }
}
