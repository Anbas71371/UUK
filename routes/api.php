<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BuatAkunController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [RegisterController::class, 'store']);
Route::get('/nama', [RegisterController::class, 'show']);
Route::get('/data', [RegisterController::class, 'data']);
Route::get('/userId/{id}', [RegisterController::class, 'userId']);
Route::get('getUsername/{id}', 'App\Http\Controllers\RegisterController@getUsername');
Route::get('getLoggedInUserId', 'App\Http\Controllers\RegisterController@getLoggedInUserId');

Route::post('/admin/buatakun', [BuatAkunController::class, 'createAccount']);
Route::get('/pelanggan', [BuatAkunController::class, 'show']);
Route::put('/editpelanggan/{id}', [BuatAkunController::class, 'update']);

// Route::post('/penjualan',[PenjualanController::class,'createAccount']);  
Route::get('/penjualan',[PenjualanController::class,'show']);
Route::post('/penjualan', [ProductController::class, 'addPenjualan']); // Jika menggunakan metode di ProductController


Route::post('product', [ProductController::class, 'index']);
Route::post('buatproduct', [ProductController::class, 'store']);
Route::get('/product', [ProductController::class, 'show']);
Route::put('/product/{id}', [ProductController::class, 'update']);
Route::put('/products/{id}', [ProductController::class, 'updates']);
Route::delete('/product/{id}', [ProductController::class, 'delete']);











Route::post('/login', [LoginController::class, 'login']); // Menggunakan metode login pada LoginController
// Route::post('/logout', [LoginController::class, 'logout']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:api');

