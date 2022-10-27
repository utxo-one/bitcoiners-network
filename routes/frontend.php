<?php

use App\Http\Controllers\Frontend\Follow\FollowActionController;
use App\Http\Controllers\Frontend\Follow\FollowRequestController;
use App\Http\Controllers\Frontend\Follow\MassFollowController;
use App\Http\Controllers\Frontend\Follow\Scopes\AvailableFollowsController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowerByUsernameController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowerController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowingByUsernameController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowingController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowRequestScopeController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LeaderboardController;
use App\Http\Controllers\Frontend\MetricController;
use App\Http\Controllers\Frontend\RatesController;
use App\Http\Controllers\Frontend\Transaction\DebitController;
use App\Http\Controllers\Frontend\Transaction\DepositController;
use App\Http\Controllers\Frontend\TweetController;
use App\Http\Controllers\Frontend\User\AvailableBalanceController;
use App\Http\Controllers\Frontend\User\ClassificationVoteController;
use App\Http\Controllers\Frontend\User\EndorsementController;
use App\Http\Controllers\Frontend\User\FollowDataController;
use App\Http\Controllers\Frontend\User\RefreshUserController;
use App\Http\Controllers\Frontend\User\UserController;
use App\Http\Controllers\Frontend\UserActionController;
use App\Http\Controllers\Web\Auth\TwitterAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/profile-pictures', [HomeController::class, 'profilesPictures'])->name('home.profile-pictures');
Route::get('/random-bitcoiners', [HomeController::class, 'randomBitcoiners'])->name('home.random-bitcoiners');
Route::get('/random-shitcoiners', [HomeController::class, 'randomShitcoiners'])->name('home.random-shitcoiners');
Route::get('/random-nocoiners', [HomeController::class, 'randomNocoiners'])->name('home.random-nocoiners');
Route::get('/rates', [RatesController::class, 'index'])->name('rates.index');
Route::get('/endorsement-types', [EndorsementController::class, 'types'])->name('endorsements.types');
Route::get('/metrics/total-bitcoiners', [MetricController::class, 'totalBitcoiners'])->name('metrics.total-bitcoiners');
Route::get('/metrics/total-shitcoiners', [MetricController::class, 'totalShitcoiners'])->name('metrics.total-shitcoiners');
Route::get('/metrics/total-nocoiners', [MetricController::class, 'totalNocoiners'])->name('metrics.total-nocoiners');
Route::get('/user/{username}', [UserController::class, 'show'])->name('user.show');
Route::get('/user/{username}/follow-data', [FollowDataController::class, 'show'])->name('user.follow-data.show');
Route::get('/logout', [TwitterAuthController::class, 'logout'])->name('logout');

Route::get('/leaderboards/bitcoiners/followers/bitcoiners', [LeaderboardController::class, 'bitcoinersByBitcoinerFollowers'])->name('leaderboards.bitcoiners.followed-by.bitcoiners');
Route::get('/leaderboards/bitcoiners/following/bitcoiners', [LeaderboardController::class, 'bitcoinersByBitcoinersFollowing'])->name('leaderboards.bitcoiners.following.bitcoiners');
Route::get('/leaderboards/bitcoiners/followers/shitcoiners', [LeaderboardController::class, 'bitcoinersByShitcoinerFollowers'])->name('leaderboards.bitcoiners.followed-by.shitcoiners');
Route::get('/leaderboards/bitcoiners/following/shitcoiners', [LeaderboardController::class, 'bitcoinersByShitcoinersFollowing'])->name('leaderboards.bitcoiners.following.shitcoiners');
Route::get('/leaderboards/bitcoiners/followers/nocoiners', [LeaderboardController::class, 'bitcoinersByNocoinerFollowers'])->name('leaderboards.bitcoiners.followed-by.nocoiners');
Route::get('/leaderboards/bitcoiners/following/nocoiners', [LeaderboardController::class, 'bitcoinersByNocoinersFollowing'])->name('leaderboards.bitcoiners.following.nocoiners');

Route::get('/leaderboards/bitcoiners/tweets/retweets/today', [LeaderboardController::class, 'bitcoinerMostRetweetedToday'])->name('leaderboards.bitcoiners.tweets.retweets.today');
Route::get('/leaderboards/bitcoiners/tweets/retweets/week', [LeaderboardController::class, 'bitcoinerMostRetweetedThisWeek'])->name('leaderboards.bitcoiners.tweets.retweets.week');
Route::get('/leaderboards/bitcoiners/tweets/retweets/month', [LeaderboardController::class, 'bitcoinerMostRetweetedThisMonth'])->name('leaderboards.bitcoiners.tweets.retweets.month');
Route::get('/leaderboards/bitcoiners/tweets/retweets/year', [LeaderboardController::class, 'bitcoinerMostRetweetedThisYear'])->name('leaderboards.bitcoiners.tweets.retweets.year');
Route::get('/leaderboards/bitcoiners/tweets/retweets/all-time', [LeaderboardController::class, 'bitcoinerMostRetweetedAllTime'])->name('leaderboards.bitcoiners.tweets.retweets.all-time');

Route::get('leaderboards/bitcoiners/tweets/likes/today', [LeaderboardController::class, 'bitcoinerMostLikedToday'])->name('leaderboards.bitcoiners.tweets.likes.today');
Route::get('leaderboards/bitcoiners/tweets/likes/week', [LeaderboardController::class, 'bitcoinerMostLikedThisWeek'])->name('leaderboards.bitcoiners.tweets.likes.week');
Route::get('leaderboards/bitcoiners/tweets/likes/month', [LeaderboardController::class, 'bitcoinerMostLikedThisMonth'])->name('leaderboards.bitcoiners.tweets.likes.month');
Route::get('leaderboards/bitcoiners/tweets/likes/year', [LeaderboardController::class, 'bitcoinerMostLikedThisYear'])->name('leaderboards.bitcoiners.tweets.likes.year');
Route::get('leaderboards/bitcoiners/tweets/likes/all-time', [LeaderboardController::class, 'bitcoinerMostLikedAllTime'])->name('leaderboards.bitcoiners.tweets.likes.all-time');

Route::get('/leaderboards/bitcoiners/tweets/replies/today', [LeaderboardController::class, 'bitcoinerMostRepliesToday'])->name('leaderboards.bitcoiners.tweets.replies.today');
Route::get('/leaderboards/bitcoiners/tweets/replies/week', [LeaderboardController::class, 'bitcoinerMostRepliesThisWeek'])->name('leaderboards.bitcoiners.tweets.replies.week');
Route::get('/leaderboards/bitcoiners/tweets/replies/month', [LeaderboardController::class, 'bitcoinerMostRepliesThisMonth'])->name('leaderboards.bitcoiners.tweets.replies.month');
Route::get('/leaderboards/bitcoiners/tweets/replies/year', [LeaderboardController::class, 'bitcoinerMostRepliesThisYear'])->name('leaderboards.bitcoiners.tweets.replies.year');
Route::get('/leaderboards/bitcoiners/tweets/replies/all-time', [LeaderboardController::class, 'bitcoinerMostRepliesAllTime'])->name('leaderboards.bitcoiners.tweets.replies.all-time');


Route::middleware('auth')->group(function () {
    Route::get('/follow/available/{userType}', [AvailableFollowsController::class, 'index'])->name('follow.available.type');
    Route::get('/follow/followers/{userType}', [FollowerController::class, 'index'])->name('follow.followers.type');
    Route::get('/follow/following/{userType}', [FollowingController::class, 'index'])->name('follow.following.type');
    Route::get('/follow/following', [FollowingController::class, 'all'])->name('follow.following.all');
    Route::get('/follow/followers', [FollowerController::class, 'all'])->name('follow.followers.all');
    Route::get('/follow/mass-follow', [MassFollowController::class, 'index'])->name('follow.mass-follow.index');
    Route::post('follow/mass-follow', [MassFollowController::class, 'store'])->name('follow.mass-follow.store');
    Route::get('follow/requests/completed', [FollowRequestScopeController::class, 'completed'])->name('follow.requests.completed');
    Route::get('follow/requests/pending', [FollowRequestScopeController::class, 'pending'])->name('follow.requests.pending');
    Route::delete('follow/mass-follow', [MassFollowController::class, 'delete'])->name('follow.mass-follow.delete');
    Route::delete('follow/requests', [FollowRequestController::class, 'delete'])->name('follow.requests.delete');
    Route::get('follow/user/followers/{username}/{userType}', [FollowerByUsernameController::class, 'index'])->name('follow.user.followers.type');
    Route::get('follow/user/followers/{username}', [FollowerByUsernameController::class, 'all'])->name('follow.user.followers.all');
    Route::get('follow/user/following/{username}/{userType}', [FollowingByUsernameController::class, 'index'])->name('follow.user.following.type');
    Route::get('follow/user/following/{username}', [FollowingByUsernameController::class, 'all'])->name('follow.user.following.all');


    Route::get('current-user/available-balance', [AvailableBalanceController::class, 'index'])->name('user.available-balance');
    Route::post('transaction/deposit', [DepositController::class, 'store'])->name('transaction.deposit.store');
    Route::get('transaction/deposit', [DepositController::class, 'index'])->name('transaction.deposit.index');
    Route::get('transaction/debit', [DebitController::class, 'index'])->name('transaction.debit.index');
    
    Route::get('/current-user/auth', [UserController::class, 'auth'])->name('user.auth');
    Route::post('/refresh/user/{username}', [RefreshUserController::class, 'store'])->name('user.refresh.store');


    Route::post('/endorse', [EndorsementController::class, 'store'])->name('user.endorse.store');
    Route::delete('/endorse', [EndorsementController::class, 'destroy'])->name('user.endorse.destroy');
    Route::get('/endorsements/{twitterId}', [EndorsementController::class, 'index'])->name('user.endorse.index');

    Route::post('/classify/{username}/{type}', [ClassificationVoteController::class, 'store'])->name('user.classify.store');
    Route::delete('/classify/{username}', [ClassificationVoteController::class, 'destroy'])->name('user.classify.destroy');
    Route::get('/classification/{username}', [ClassificationVoteController::class, 'index'])->name('user.classify.index');
    Route::post('/close-classification-tip', [ClassificationVoteController::class, 'closeVoteTooltip'])->name('user.classify.close-tip');

    Route::post('/action/{username}/follow', [FollowActionController::class, 'follow'])->name('user.action.follow');
    Route::delete('/action/{username}/unfollow', [FollowActionController::class, 'unfollow'])->name('user.action.unfollow');

    Route::post('/action/{username}/block', [UserActionController::class, 'block'])->name('user.action.block');
    Route::delete('/action/{username}/unblock', [UserActionController::class, 'unblock'])->name('user.action.unblock');

    Route::post('/action/{username}/mute', [UserActionController::class, 'mute'])->name('user.action.mute');
    Route::delete('/action/{username}/unmute', [UserActionController::class, 'unmute'])->name('user.action.unmute');

    Route::post('/action/{tweetId}/like', [UserActionController::class, 'like'])->name('user.action.like');
    Route::delete('/action/{tweetId}/unlike', [UserActionController::class, 'unlike'])->name('user.action.unlike');

    Route::post('/action/{tweetId}/retweet', [UserActionController::class, 'retweet'])->name('user.action.retweet');
    Route::delete('/action/{tweetId}/unretweet', [UserActionController::class, 'unretweet'])->name('user.action.unretweet');

});


