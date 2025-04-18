<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    //
    // app/Http/Controllers/DashboardController.php
    public function dashboard()
    {
        $posts = Post::with(['user', 'comments.user'])->latest()->get();
        $users = User::all();
        return view('dashboard', compact('posts', 'users'));
    }
}


