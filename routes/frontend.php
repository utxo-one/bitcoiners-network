<?php

use App\Http\Controllers\Frontend\Follow\FollowRequestController;
use App\Http\Controllers\Frontend\Follow\MassFollowController;
use App\Http\Controllers\Frontend\Follow\Scopes\AvailableFollowsController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowerController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowingController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowRequestScopeController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RatesController;
use App\Http\Controllers\Frontend\Transaction\DepositController;
use App\Http\Controllers\Frontend\User\AvailableBalanceController;
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

// If environment is local, Authenticate user with twitter_id 1558929312547577858
if (app()->environment('local')) {
    Auth::loginUsingId('1558929312547577858');
}

Route::get('/profile-pictures', [HomeController::class, 'profilesPictures'])->name('home.profile-pictures');
Route::get('/random-bitcoiners', [HomeController::class, 'randomBitcoiners'])->name('home.random-bitcoiners');
Route::get('/random-shitcoiners', [HomeController::class, 'randomShitcoiners'])->name('home.random-shitcoiners');
Route::get('/random-nocoiners', [HomeController::class, 'randomNocoiners'])->name('home.random-nocoiners');
Route::get('/rates', [RatesController::class, 'index'])->name('rates.index');

Route::middleware('auth')->group(function () {
    Route::get('/follow/available/{userType}', [AvailableFollowsController::class, 'index'])->name('follow.available');
    Route::get('/follow/followers/{userType}', [FollowerController::class, 'index'])->name('follow.followers');
    Route::get('/follow/following/{userType}', [FollowingController::class, 'index'])->name('follow.following');
    Route::get('/follow/mass-follow', [MassFollowController::class, 'index'])->name('follow.mass-follow.index');
    Route::post('follow/mass-follow', [MassFollowController::class, 'store'])->name('follow.mass-follow.store');
    Route::get('follow/requests/completed', [FollowRequestScopeController::class, 'completed'])->name('follow.requests.completed');
    Route::get('follow/requests/pending', [FollowRequestScopeController::class, 'pending'])->name('follow.requests.pending');
    Route::delete('follow/mass-follow', [MassFollowController::class, 'delete'])->name('follow.mass-follow.delete');
    Route::delete('follow/requests', [FollowRequestController::class, 'delete'])->name('follow.requests.delete');
    Route::get('user/available-balance', [AvailableBalanceController::class, 'index'])->name('user.available-balance');
    Route::post('transaction/deposit', [DepositController::class, 'store'])->name('transaction.deposit.store');
    Route::get('/user/auth', [UserController::class, 'auth'])->name('user.auth');
    Route::get('/user/{username}', [UserController::class, 'show'])->name('user.show');
});


