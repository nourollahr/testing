<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UploadImageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SingleController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/single/{post}', [SingleController::class, 'index'])->name('single');
Route::post('/single/{post}/comment', [SingleController::class, 'comment'])
    ->middleware('auth:web')->name('single.comment');

Route::prefix('admin')->middleware('admin')->group(function() {
    Route::resource('post', PostController::class)->except('show');
    Route::resource('user', UserController::class);
    Route::post('/upload', [UploadImageController::class, 'upload'])->name('upload');
});

Auth::routes();