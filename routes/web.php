<?php

use Azuriom\Plugin\DiscordAuth\Controllers\DiscordAuthHomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your plugin. These
| routes are loaded by the RouteServiceProvider of your plugin within
| a group which contains the "web" middleware group and your plugin name
| as prefix. Now create something great!
|
*/

Route::get('/', [DiscordAuthHomeController::class, 'redirectToProvider'])->name('login');
Route::get('/callback', [DiscordAuthHomeController::class, 'handleProviderCallback']);
Route::post('/register-username', [DiscordAuthHomeController::class, 'registerUsername'])->name('register-username');
Route::get('/username', [DiscordAuthHomeController::class, 'username'])->name('username');
