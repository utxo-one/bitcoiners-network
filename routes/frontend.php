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
use App\Http\Controllers\Frontend\SearchController;
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
Route::get('/endorsements/user/{username}', [EndorsementController::class, 'index'])->name('user.endorse.index');
Route::get('/endorsements/type/{type}', [EndorsementController::class, 'typeIndex'])->name('type-index.type.index');
Route::get('/search', [SearchController::class, 'autoFill'])->name('search.auto-fill');
Route::post('/search', [SearchController::class, 'search'])->name('search.search');
Route::get('/timeline/{username}', [TweetController::class, 'show'])->name('timeline.show');

Route::get('/leaderboard/users/{userType}/{followType}/{followedByType}/between/{minFollowers}/{maxFollowers}', [LeaderboardController::class, 'users'])->name('leaderboards.users');
Route::get('/leaderboard/tweets/{userType}/{orderBy}/days/{timeframe}', [LeaderboardController::class, 'tweets'])->name('leaderboards.tweets');

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
    Route::get('/endorsements/user/{username}/auth', [EndorsementController::class, 'index'])->name('auth-user.user.endorse.index');
 

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


