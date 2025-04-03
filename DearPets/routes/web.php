<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

//use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
//use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard showing personal feed
Route::get('/dashboard', [PostController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Post creation and favoriting
    Route::post('/post', [PostController::class, 'create'])->name('post.create');
    Route::post('/post/{post}/favorite', [PostController::class, 'favorite'])->name('post.favorite');

    // Search & Viewing other profiles
    Route::get('/search', [UserController::class, 'search'])->name('user.search');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.profile');

    Route::get('/profile/{id}', [UserController::class, 'show'])->name('profile.view');

});

require __DIR__.'/auth.php';
Route::post('/post/{post}/comment', [CommentController::class, 'store'])->name('post.comment');

