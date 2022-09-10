<?php

use App\Http\Controllers\Web\Auth\TwitterAuthController;
use App\Http\Controllers\Web\Follow\Scopes\AvailableFollowsController;
use App\Http\Controllers\Web\Follow\Scopes\FollowerController;
use App\Http\Controllers\Web\Follow\Scopes\FollowingController;
use App\Http\Controllers\Web\Transaction\BtcPayWebhookController;
use App\Http\Controllers\Web\Transaction\DepositController;
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

    Route::get('/transaction/deposit', [DepositController::class, 'index'])->name('transaction.deposit.index');
    Route::post('/transaction/deposit', [DepositController::class, 'store'])->name('transaction.deposit.store');
    Route::get('/transaction/deposit/success', [DepositController::class, 'show'])->name('transaction.deposit.show');

    Route::post('/transaction/btcpay/webhook', [BtcPayWebhookController::class, 'index'])->name('transaction.btcpay.webhook');
});
