<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\DashboardController;

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (personal feed or dashboard home)
Route::get('/dashboard', [PostController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts & Favorites
    Route::post('/post', [PostController::class, 'create'])->name('post.create');
    Route::post('/post/{post}/favorite', [PostController::class, 'favorite'])->name('post.favorite');

    // Comments
    Route::post('/post/{post}/comment', [CommentController::class, 'store'])->name('post.comment');

    // User Search & Profiles
    Route::get('/search', [UserController::class, 'search'])->name('user.search');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.profile');
    Route::get('/profile/{id}', [UserController::class, 'show'])->name('profile.view');

    // Messaging System
    Route::post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/{userId}', [MessageController::class, 'fetchMessages'])->name('messages.fetch');
    Route::get('/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/chat/{user}', [MessageController::class, 'chatWithUser'])->name('chat.show');
});

// Auth routes (login, register, etc.)
require __DIR__.'/auth.php';


//friendship
// routes/web.php

use App\Http\Controllers\FriendshipController;

Route::middleware(['auth'])->group(function () {
    Route::get('/friend-requests', [FriendshipController::class, 'requests'])->name('friend.requests');
    Route::post('/friend/accept/{id}', [FriendshipController::class, 'accept'])->name('friend.accept');
    Route::post('/friend/reject/{id}', [FriendshipController::class, 'reject'])->name('friend.reject');
});

Route::post('/friend/request/{id}', [App\Http\Controllers\FriendshipController::class, 'sendRequest'])->name('friend.request');
Route::post('/friend/unfriend/{user_id}', [FriendshipController::class, 'unfriend'])->name('friend.unfriend');

Route::get('/profile/{id}', [\App\Http\Controllers\FriendshipController::class, 'showFriendList'])->name('profile.show');



