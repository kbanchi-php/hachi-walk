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

// routing of root
Route::get('/', [
    App\Http\Controllers\WalkController::class, 'index'
])->name('root');

// routing of walks resource with auth
Route::resource('walks', App\Http\Controllers\WalkController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');

// routing of walks resource without auth
Route::resource('walks', App\Http\Controllers\WalkController::class)
    ->only(['index', 'show']);

require __DIR__ . '/auth.php';

// routing of sns auth
Route::prefix('auth')->middleware('guest')->group(function () {
    // auth/{provider}
    Route::get('/{provider}', [
        App\Http\Controllers\Auth\OAuthController::class, 'redirectToProvider'
    ])->where('provider', 'github|google|line|facebook')->name('redirectToProvider');

    // auth/{provider}/callbak
    Route::get('/{provider}/callback', [
        App\Http\Controllers\Auth\OAuthController::class, 'oauthCallback'
    ])->where('provider', 'github|google|line|facebook')->name('oauthCallback');
});
