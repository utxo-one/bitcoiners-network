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
    return view('landing-page');
})->name('landing_page');

Route::get('/get_started', function () {
    return view('get-started');
})->name('get_started');

Route::get('/terms', function () { 
    return view('terms');
});

Route::get('/privacy', function () { 
    return view('privacy');
});

Route::get('/auth/twitter', [TwitterAuthController::class, 'login'])->name('twitter.login');
Route::get('/auth/twitter/callback', [TwitterAuthController::class, 'callback'])->name('twitter.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {

    Route::get('/u', function () {
        return view('react-app');
    })->name('react-app');

    // Route::get('/transaction/deposit', [DepositController::class, 'index'])->name('transaction.deposit.index');
    // Route::post('/transaction/deposit', [DepositController::class, 'store'])->name('transaction.deposit.store');
    Route::get('/transaction/deposit/success', [DepositController::class, 'show'])->name('transaction.deposit.show');
});

// Allow non-auth users to see user profiles:
Route::get('/u/profile/{username}', function () {
    return view('react-app');
});

// All other React routes will require the user to be authenticated:
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::view('/u/{path?}', 'react-app')
     ->where('path', '.*')
     ->name('react-app');
});

Route::post('/transaction/btcpay/webhook', [BtcPayWebhookController::class, 'index'])->name('transaction.btcpay.webhook');