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

Route::get('/login', [\App\Http\Controllers\LoginController::class, 'index'])->name('login');
Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->name('logout');

Route::middleware([\App\Http\Middleware\CheckSession::class])->group(function () {
    Route::get('/', function () {
        return redirect()->route('home');
    });

    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
