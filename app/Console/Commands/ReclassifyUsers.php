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
        // // ask the console what user type to reclassify
        // $userType = $this->choice('What user type do you want to reclassify?', [
        //     UserType::BITCOINER->value,
        //     UserType::SHITCOINER->value,
        //     UserType::NOCOINER->value,
        // ]);

        // // ask the console how many users to reclassify
        // $count = $this->ask('How many users do you want to reclassify?');

        // get 1000 users of the given type who with last_classified_at null or older than 1 day and classified_by is not 'vote'
        $users = User::query()
            ->where('type', UserType::NOCOINER->value)
            ->where(function ($query) {
                $query->where('last_classified_at', NULL);
            })
            ->orWhere(function ($query) {
                $query->where('last_classified_at', '<', now()->subDay());
            })
            ->where('classified_by', '!=', ClassificationSource::VOTE)
            ->inRandomOrder()
            ->limit(5000)
            ->get();

        $userIds = [];
       
        // for each user, reclassify them with the user service classify method
        foreach ($users as $user) {
            $userService = new UserService();
            $newType = $userService->classifyUser(user: $user);
            $userIds[] = $user->twitter_id;

            // if the new type is different the current type, display a message in the console with the new type, and update the last_classified_at and classified_by fields
            if ($newType->value != $user->type) {
                $this->info('user ' . $user->twitter_username . ' changed from ' . $user->type . ' to ' . $newType->value);
                //Log::info('user ' . $user->twitter_username . ' changed from ' . $user->type . ' to ' . $newType->value);

                $user->type = $newType;
                $user->last_classified_at = now();
                $user->classified_by = ClassificationSource::CRAWLER;
                $user->save();
            }
        }
    }
}
