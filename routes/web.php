<?php

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

Route::get('/', [
    App\Http\Controllers\WalkController::class, 'index'
])->name('root');

Route::resource('walks', App\Http\Controllers\WalkController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');

Route::resource('walks', App\Http\Controllers\WalkController::class)
    ->only(['index', 'show']);

require __DIR__ . '/auth.php';

// authから始まるルーティングに認証前にアクセスがあった場合
Route::prefix('auth')->middleware('guest')->group(function () {
    // auth/githubにアクセスがあった場合はOAuthControllerのredirectToProviderアクションへルーティング
    Route::get('/{provider}', [
        App\Http\Controllers\Auth\OAuthController::class, 'redirectToProvider'
    ])->where('provider', 'github|google|line')->name('redirectToProvider');

    // auth/github/callbackにアクセスがあった場合はOAuthControllerのoauthCallbackアクションへルーティング
    Route::get('/{provider}/callback', [
        App\Http\Controllers\Auth\OAuthController::class, 'oauthCallback'
    ])->where('provider', 'github|google|line')->name('oauthCallback');
});
