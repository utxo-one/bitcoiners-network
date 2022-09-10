<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use App\Models\User;
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
        $totalBitcoiners = User::where('type', UserType::BITCOINER)->count();
        $this->info('starting count: ' . $totalBitcoiners);
        $this->warn('starting crawl');

        $crawlerService = new CrawlerService();
        $crawlerService->crawlBitcoiners();

        $this->warn('Done');
        $this->info('ending count: ' . User::where('type', UserType::BITCOINER)->count());

        return 0;
    }
}
