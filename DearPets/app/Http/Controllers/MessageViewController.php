<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageViewController extends Controller
{
    public function inbox()
    {
        $userId = Auth::id();

        $latestMessages = Message::where('receiver_id', $userId)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('sender_id')
            ->map(function ($messages) {
                return $messages->last(); // latest from each sender
            })
            ->values();

        return view('messages.inbox', compact('latestMessages'));
    }
}

