<?php

namespace App\Jobs;

use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use UtxoOne\TwitterUltimatePhp\Models\User as TwitterUser;

class ProcessTwitterUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TwitterUser $twitterUser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TwitterUser $twitterUser)
    {
        $this->twitterUser = $twitterUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UserService $userService)
    {
        $userService->processTwitterUser($this->twitterUser);
    }
}
