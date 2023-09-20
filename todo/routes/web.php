<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(\App\Http\Controllers\Auth\LoginRegisterController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(\App\Http\Controllers\Auth\ForgotPasswordController::class)->group(function () {
    Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');
    Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');
    Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');
    Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
});

Route::middleware(['auth'])->group(function () {
    Route::controller(\App\Http\Controllers\TodoController::class)->group(function () {
        Route::resource('todos', \App\Http\Controllers\TodoController::class);
        Route::get('todos/{todo}', 'destroy')->name('todos.destroy');
        Route::get('share-todo/{todo}', 'shareForm')->name('todo.share.form');
        Route::post('share-todo/{todo}', 'share')->name('todo.share');
        Route::get('/add-todo/{token}', 'add')->name('todo.add');
    });

    Route::controller(\App\Http\Controllers\GroupController::class)->group(function () {
        Route::resource('groups', \App\Http\Controllers\GroupController::class);
        Route::get('/groups', 'index')->name('groups.index');
        Route::get('groups/{group}', 'destroy')->name('groups.destroy');
    });
});
