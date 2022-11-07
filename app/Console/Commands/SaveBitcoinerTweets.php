<?php

namespace App\Console\Commands;

use App\Services\CrawlerService;
use Illuminate\Console\Command;

class SaveBitcoinerTweets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:bitcoiner-tweets';

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
        $crawlerService = new CrawlerService();
        $crawlerService->saveBitcoinerTweets(30);
        return 0;
    }
}
