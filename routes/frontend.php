<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Frontend\Follow\Scopes\AvailableFollowsController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowerController;
use App\Http\Controllers\Frontend\Follow\Scopes\FollowingController;
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

Route::middleware('auth')->group(function () {
    Route::get('/follow/available/{userType}', [AvailableFollowsController::class, 'index'])->name('follow.available');
    Route::get('/follow/followers/{userType}', [FollowerController::class, 'index'])->name('follow.followers');
    Route::get('/follow/following/{userType}', [FollowingController::class, 'index'])->name('follow.following');

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{username}', [UserController::class, 'show'])->name('user.show');
});


