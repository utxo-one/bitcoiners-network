<?php

namespace App\Console\Commands;

use App\Services\CrawlerService;
use App\Services\UserService;
use Illuminate\Console\Command;
use UtxoOne\TwitterUltimatePhp\Clients\UserClient;

class ProcessTwitterFollowers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:followers';

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

        $this->info('Processing followers, please wait');

        $twitterUserClient = new UserClient(bearerToken: config('services.twitter.bearer_token'));
        $twitterUser = $twitterUserClient->getUserByUsername($twitterUsername);
        $followers = $twitterUserClient->getFollowers($twitterUser->getId());

        $crawlerService = new CrawlerService();
        $crawlerService->processTwitterUsers($followers);

        $this->info('Done');

        return 0;
    }
}
