<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\LeaderboardRepository;
use App\Repositories\TweetRepository;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
        private TweetRepository $tweetRepository)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | User Leaderboards
    |--------------------------------------------------------------------------
    |
    | The User Leaderboard endpoints retrieve a cached version of the leaderboard
    | if it exists, otherwise it will generate a new leaderboard and cache it.
    |
    */

    /**
     * Bitcoiners by Bitcoiner Followers
     * 
     * Get a paginated list of bitcoiners followed by the most bitcoiners.
     * 
     * @group User
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinersByBitcoinerFollowers(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByBitcoinerFollowers();

        return response()->json($this->paginate($results));
    }
    
    /**
     * Bitcoiners by Bitcoiners Following
     * 
     * Get a paginated list of bitcoiners who follow the most bitcoiners.
     * 
     * @group User
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinersByBitcoinersFollowing(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByBitcoinersFollowing();

        return response()->json($this->paginate($results));
    }

    /**
     * Bitcoiners by Shitcoiner Followers
     * 
     * Get a paginated list of bitcoiners followed by the most shitcoiners.
     * 
     * @group User
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinersByShitcoinerFollowers(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByShitcoinerFollowers();

        return response()->json($this->paginate($results));
    }

    /**
     * Bitcoiners by Shitcoiners Following
     * 
     * Get a paginated list of bitcoiners who follow the most shitcoiners.
     * 
     * @group User
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinersByShitcoinersFollowing(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByShitcoinersFollowing();

        return response()->json($this->paginate($results));
    }

    /**
     * Bitcoiners by Nocoiner Followers
     * 
     * Get a paginated list of bitcoiners followed by the most nocoiners.
     * 
     * @group User
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinersByNocoinerFollowers(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByNocoinerFollowers();

        return response()->json($this->paginate($results));
    }

    /**
     * Bitcoiners by Nocoiners Following
     * 
     * Get a paginated list of bitcoiners who follow the most nocoiners.
     * 
     * @group User
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinersByNocoinersFollowing(): JsonResponse
    {
        $results = $this->leaderboardRepository->getBitcoinersByNocoinersFollowing();

        return response()->json($this->paginate($results));
    }    

    /*
    |--------------------------------------------------------------------------
    | Tweet Leaderboards
    |--------------------------------------------------------------------------
    |
    | The Tweet Leaderboard endpoints retrieve a cached version of the leaderboard
    | if it exists, otherwise it will generate a new leaderboard and cache it.
    |
    */

    /**
     * Most Retweeted Tweets by Bitcoiners Today
     * 
     * Get a paginated list of the most retweeted tweets by bitcoiners today.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRetweetedToday(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRetweetedToday();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Retweeted Tweets by Bitcoiners This Week
     * 
     * Get a paginated list of the most retweeted tweets by bitcoiners this week.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRetweetedThisWeek(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRetweetedThisWeek();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Retweeted Tweets by Bitcoiners This Month
     * 
     * Get a paginated list of the most retweeted tweets by bitcoiners this month.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRetweetedThisMonth(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRetweetedThisMonth();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Retweeted Tweets by Bitcoiners This Year
     * 
     * Get a paginated list of the most retweeted tweets by bitcoiners this year.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRetweetedThisYear(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRetweetedThisYear();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Retweeted Tweets by Bitcoiners All Time
     * 
     * Get a paginated list of the most retweeted tweets by bitcoiners all time.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRetweetedAllTime(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRetweetedAllTime();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Liked Tweets by Bitcoiners Today
     * 
     * Get a paginated list of the most liked tweets by bitcoiners today.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostLikedToday(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostLikedToday();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Liked Tweets by Bitcoiners This Week
     * 
     * Get a paginated list of the most liked tweets by bitcoiners this week.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostLikedThisWeek(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostLikedThisWeek();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Liked Tweets by Bitcoiners This Month
     * 
     * Get a paginated list of the most liked tweets by bitcoiners this month.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostLikedThisMonth(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostLikedThisMonth();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Liked Tweets by Bitcoiners This Year
     * 
     * Get a paginated list of the most liked tweets by bitcoiners this year.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostLikedThisYear(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostLikedThisYear();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Liked Tweets by Bitcoiners All Time
     * 
     * Get a paginated list of the most liked tweets by bitcoiners all time.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostLikedAllTime(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostLikedAllTime();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Replied To Tweets by Bitcoiners Today
     * 
     * Get a paginated list of the most replied to tweets by bitcoiners today.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRepliesToday(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRepliesToday();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Replied To Tweets by Bitcoiners This Week
     * 
     * Get a paginated list of the most replied to tweets by bitcoiners this week.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRepliesThisWeek(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRepliesThisWeek();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Replied To Tweets by Bitcoiners This Month
     * 
     * Get a paginated list of the most replied to tweets by bitcoiners this month.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRepliesThisMonth(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRepliesThisMonth();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Replied To Tweets by Bitcoiners This Year
     * 
     * Get a paginated list of the most replied to tweets by bitcoiners this year.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRepliesThisYear(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRepliesThisYear();

        return response()->json($this->paginate($results));
    }

    /**
     * Most Replied To Tweets by Bitcoiners All Time
     * 
     * Get a paginated list of the most replied to tweets by bitcoiners all time.
     * 
     * @group Tweet
     * 
     * @queryParam page The page number. Example: 1
     * @queryParam per_page The number of items per page. Example: 100
     * 
     * @return JsonResponse
     */
    public function bitcoinerMostRepliesAllTime(): JsonResponse
    {
        $results = $this->tweetRepository->getBitcoinerMostRepliesAllTime();

        return response()->json($this->paginate($results));
    }

}
