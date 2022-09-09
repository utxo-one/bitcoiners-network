<?php

namespace App\Console\Commands;

use App\Services\CrawlerService;
use Illuminate\Console\Command;

class CrawlBitcoiners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:bitcoiners';

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

        $this->info('crawling bitcoiners, please wait');

        $crawlerService = new CrawlerService();
        $crawlerService->crawlBitcoiners();

        $this->info('Done');

        return 0;
    }
}
