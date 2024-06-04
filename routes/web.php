<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;


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

Route::post('/login',[LoginController::class, 'check']);

Route::post('/login', [LoginController::class, 'login'])->name('login');


Route::post('/register',[RegisterController::class, 'store']);

Route::resource('/pinjaman', 'App\Http\Controllers\PinjamanController');
