<?php

namespace App\Console\Commands;

use App\Enums\ClassificationSource;
use App\Enums\UserType;
use App\Models\User;
use App\Services\CrawlerService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReclassifyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reclassify:users';

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
        $userService = new UserService();

        User::chunk(10000, function ($users) use ($userService) {
            foreach ($users as $user) {

                $newType = $userService->classifyUser(user: $user);
                $userIds[] = $user->twitter_id;
    
                if ($newType->value != $user->type) {
                    $this->info('user ' . $user->twitter_username . ' changed from ' . $user->type . ' to ' . $newType->value);
   
                    $user->type = $newType;
                    $user->last_classified_at = now();
                    $user->classified_by = ClassificationSource::CRAWLER;
                    $user->save();
                }
            }
        });
    }
}
