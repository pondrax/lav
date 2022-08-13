<?php

use App\Http\Controllers\App\OauthController;
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
})->name('login');

Route::get('/api/auth/{provider}', [OauthController::class, 'redirect'])->name('oauth.handle');
Route::get('/api/auth/{provider}/callback', [OauthController::class, 'callback'])->name('oauth.callback');
