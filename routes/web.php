<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'posts');

// Posts Route
Route::resource('posts', PostController::class);

// User Posts Route
Route::get('/{user}/posts',  [DashboardController::class, 'userPosts'])->name('posts.user');

// Routes For Authenticated Users
Route::middleware('auth')->group(function(){
    Route::get('/dashboard',  [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // The Email Verification Notice
    Route::get('/email/verify', [AuthController::class , 'verifyNotice'])->name('verification.notice');

    // The Email Verification Handler
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->
    middleware('signed')->name('verification.verify');

    // Resending the Verification Email
    Route::post('/email/verification-notification', [AuthController::class,'verifyHandler'])->
    middleware('throttle:6,1')->name('verification.send');
});

// Routes For Guest Users
Route::middleware('guest')->group(function(){
    
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Routes For Reset Password
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

    Route::post('/forgot-password', [ResetPasswordController::class, 'passwordEmail'] );

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'passwordReset'])->
    name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->
    name('password.update');
});

