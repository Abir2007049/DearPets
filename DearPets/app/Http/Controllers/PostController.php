<?php

namespace App\Http\Controllers;

use App\Models\Post; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use Illuminate\Http\Request; 


class PostController extends Controller
{
    public function index()
{
    $posts = Post::where('user_id', '!=', auth()->id())->orderBy('created_at', 'desc')->get();
    $users = User::where('id', '!=', auth()->id())->get(); // 👈 Add this line

    return view('dashboard', compact('posts', 'users')); // 👈 Now you're passing $users
}


    public function create(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'caption' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi|max:10240',
        ]);

        // Handle file uploads
        $imagePath = null;
        $videoPath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('posts', 'public');
        }

        // Create new post
        Post::create([
            'user_id' => auth()->id(),
            'caption' => $validated['caption'],
            'image' => $imagePath,
            'video' => $videoPath,
        ]);

        return back()->with('success', 'Post created successfully!');
    }
    public function favorite(Post $post)
{
    $user = Auth::user();

    if ($post->isLikedBy($user)) {
        $post->likes()->where('user_id', $user->id)->delete();
        return response()->json(['liked' => false]);
    } else {
        $post->likes()->create(['user_id' => $user->id]);
        return response()->json(['liked' => true]);
    }
}


    

}
