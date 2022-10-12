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
use App\Http\Controllers\Frontend\MetricController;
use App\Http\Controllers\Frontend\RatesController;
use App\Http\Controllers\Frontend\Transaction\DebitController;
use App\Http\Controllers\Frontend\Transaction\DepositController;
use App\Http\Controllers\Frontend\User\AvailableBalanceController;
use App\Http\Controllers\Frontend\User\ClassificationVoteController;
use App\Http\Controllers\Frontend\User\EndorsementController;
use App\Http\Controllers\Frontend\User\RefreshUserController;
use App\Http\Controllers\Frontend\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

#If environment is local, Authenticate user with twitter_id 1558929312547577858
// if (app()->environment('local')) {
//     Auth::loginUsingId('1558929312547577858');
// }

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

    Route::post('/action/{username}/follow', [FollowActionController::class, 'follow'])->name('user.action.follow');
    Route::delete('/action/{username}/unfollow', [FollowActionController::class, 'unfollow'])->name('user.action.unfollow');

});


