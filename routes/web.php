<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/upload-image', [\App\Http\Controllers\HomeController::class, 'uploadforPrediction'])->name('submitData')->middleware('auth');
Route::get('/search', [\App\Http\Controllers\HomeController::class, 'searchLogs'])->name('search');
Route::get('/logs', [App\Http\COntrollers\HomeController::class, 'getLogs'])->name('logs');
Route::post('prediction/callback', [\App\Http\Controllers\HomeController::class, 'predictionCallback']);
Route::get('/zipped/create', [App\Http\Controllers\HomeController::class, 'zipImage']
);

