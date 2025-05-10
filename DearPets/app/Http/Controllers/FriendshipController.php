<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FriendshipController extends Controller
{
    //
    // app/Http/Controllers/FriendshipController.php

    public function sendRequest($id)
{
    // Check if a friend request already exists
    $existingRequest = Friendship::where(function ($q) use ($id) {
        $q->where('sender_id', Auth::id())->where('receiver_id', $id);
    })->orWhere(function ($q) use ($id) {
        $q->where('receiver_id', Auth::id())->where('sender_id', $id);
    })->first();

    // If no request exists, create a new one
    if (!$existingRequest) {
        Friendship::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $id,
            'status' => 'pending',
        ]);
    }

    return redirect()->back()->with('success', 'Friend request sent!');
}

    

public function accept($friendship_id)
{
    $friendship = Friendship::findOrFail($friendship_id);
    if ($friendship->receiver_id == Auth::id()) {
        $friendship->update(['status' => 'accepted']);
    }

    return back();
}

public function reject($friendship_id)
{
    $friendship = Friendship::findOrFail($friendship_id);
    if ($friendship->receiver_id == Auth::id()) {
        $friendship->update(['status' => 'rejected']);
    }

    return back();
}

public function unfriend($user_id)
{
    Friendship::where(function ($q) use ($user_id) {
        $q->where('sender_id', Auth::id())->where('receiver_id', $user_id);
    })->orWhere(function ($q) use ($user_id) {
        $q->where('receiver_id', Auth::id())->where('sender_id', $user_id);
    })->delete();

    return back();
}
public function requests()
{
    $requests = Friendship::where('receiver_id', Auth::id())
        ->where('status', 'pending')
        ->get();

    return view('friendreq', compact('requests'));
}


public function showFriendList($id)
{
    $user = User::findOrFail($id);

    $friends = Friendship::where(function ($q) use ($user) {
        $q->where('sender_id', $user->id)
          ->orWhere('receiver_id', $user->id);
    })
    ->where('status', 'accepted')
    ->get()
    ->map(function ($friendship) use ($user) {
        return $friendship->sender_id == $user->id
            ? User::find($friendship->receiver_id)
            : User::find($friendship->sender_id);
    });

    return view('user-profile', compact('user', 'friends'));
}



}
