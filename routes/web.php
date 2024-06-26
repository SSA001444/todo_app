<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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
    Route::get('/dashboard', 'dashboard')->name('dashboard')->middleware('auth', 'is_verify_email');
    Route::get('account/verify/{token}', 'verifyAccount')->name('user.verify');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(\App\Http\Controllers\Auth\ForgotPasswordController::class)->group(function () {
    Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');
    Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');
    Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');
    Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
});

Route::post('/locale-switch', [\App\Http\Controllers\LanguageController::class, 'switchLocale'])->name('locale.switch');

Route::middleware(['auth'])->group(function () {

    Route::controller(\App\Http\Controllers\TeamStatusController::class)->group(function () {
        Route::get('/team-status', 'showTeamStatus')->name('team-status');
    });

    Route::controller(\App\Http\Controllers\TeamController::class)->group(function () {
        Route::post('/teams', 'store')->name('teams.store');
        Route::get('/teams/leave', 'leave')->name('teams.leave');
        Route::get('/teams/dissolve', 'dissolve')->name('teams.dissolve');
    });

    Route::controller(\App\Http\Controllers\ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::post('/profile', 'update')->name('profile.update');
        Route::get('/profile/verify-current-email/{token}', 'verifyCurrentEmail')->name('profile.verify.current.email');
        Route::get('/profile/verify-new-email/{token}', 'verifyNewEmail')->name('profile.verify.new.email');
        Route::post('/profile/update-photo', 'updatePhoto')->name('profiles.update-photo');
    });

    Route::middleware(['check.team'])->group(function () {

        Route::middleware(['role:admin,moderator'])->group(function () {
            Route::controller(\App\Http\Controllers\AdminController::class)->group(function () {
                Route::get('/admin', 'index')->name('admin.index');
                Route::get('admin/users', 'users')->name('admin.users');
                Route::post('admin/users/invite', 'inviteUser')->name('admin.users.invite');
                Route::post('admin/users/{user}/role', 'updateUserRole')->name('admin.users.updateRole');
                Route::post('admin/users/{user}/remove', 'removeUser')->name('admin.users.remove');
            });
        });

        Route::controller(\App\Http\Controllers\TicketController::class)->group(function () {
            Route::resource('tags', \App\Http\Controllers\TagController::class);
            Route::resource('tickets', \App\Http\Controllers\TicketController::class);
            Route::get('/tickets', 'index')->name('tickets.index');
            Route::post('/tickets', 'store')->name('tickets.store');
            Route::get('/tickets/{id}', 'show')->name('tickets.show');
            Route::post('/tickets/tasks', 'storeTask')->name('tasks.store');
            Route::post('/tickets/comments', 'storeComment')->name('comments.store');
            Route::patch('/tickets/comments/{id}', 'updateComment')->name('comments.update');
            Route::delete('/tickets/comments/{id}', 'destroyComment')->name('comments.destroy');
            Route::patch('/tasks/{task}', 'updateTask')->name('tasks.update');
            Route::patch('/tickets/{id}', 'update')->name('tickets.update');
            Route::delete('/tickets/{id}', 'destroy')->name('tickets.destroy');
            Route::patch('/tickets/{ticket}/toggle-status', 'toggleStatus')->name('tickets.toggle-status');
        });

        Route::controller(\App\Http\Controllers\TagController::class)->group(function () {
            Route::resource('tags', \App\Http\Controllers\TagController::class);
            Route::get('/tags', 'index')->name('tags.index');
        });


        Route::controller(\App\Http\Controllers\MessengerController::class)->group(function () {
            Route::get('/messenger', 'index')->name('messenger.index');
            Route::get('/messenger/{contactId}', 'showDialog')->name('messenger.dialog');
            Route::post('/messenger/{contactId}', 'sendMessage')->name('messenger.send');
            Route::put('/messenger/{messageId}', 'editMessage')->name('messenger.edit');
            Route::delete('/messenger/{messageId}', 'deleteMessage')->name('messenger.delete');
        });
    });
});
