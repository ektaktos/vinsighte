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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');
Route::post('/upload-image', [\App\Http\Controllers\HomeController::class, 'uploadforPrediction'])->name('submitData');
Route::get('/zipped/create', [App\Http\Controllers\HomeController::class, 'zipImage']
);

