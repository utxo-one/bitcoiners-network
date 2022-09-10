<?php

use App\Http\Controllers\Web\Auth\TwitterAuthController;
use App\Http\Controllers\Web\Follow\Scopes\AvailableFollowsController;
use App\Http\Controllers\Web\Follow\Scopes\FollowerController;
use App\Http\Controllers\Web\Follow\Scopes\FollowingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/twitter', [TwitterAuthController::class, 'login'])->name('twitter.login');
Route::get('/auth/twitter/callback', [TwitterAuthController::class, 'callback'])->name('twitter.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/follow/available/{userType}', [AvailableFollowsController::class, 'index'])->name('follow.available');
    Route::get('/follow/followers/{userType}', [FollowerController::class, 'index'])->name('follow.followers');
    Route::get('/follow/following/{userType}', [FollowingController::class, 'index'])->name('follow.following');
});
