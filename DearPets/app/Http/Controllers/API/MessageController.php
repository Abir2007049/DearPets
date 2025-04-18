<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    // Send a new message
    public function sendMessage(Request $request)
    {
        \Log::info('Incoming message request', $request->all());
    
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);
    
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);
    
        \Log::info('Message created', ['message' => $message]);
    
        return response()->json(['message' => 'Message sent!', 'data' => $message], 201);
    }
    

    // Fetch full conversation with a specific user
    public function fetchMessages($userId)
    {
        $messages = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($userId) {
                $q->where('receiver_id', auth()->id())
                  ->where('sender_id', $userId);
            })
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    // Show inbox: list of latest messages from each sender
    public function inbox()
    {
        $userId = auth()->id();

        // Get all received and sent messages
        $allMessages = Message::where('receiver_id', $userId)
            ->orWhere('sender_id', $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        $conversations = [];

        foreach ($allMessages as $message) {
            $otherUserId = $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;

            if (!isset($conversations[$otherUserId])) {
                $conversations[$otherUserId] = $message;
            }
        }

        return response()->json(array_values($conversations));
    }
    public function chatWithUser(User $user)
{
    $messages = Message::where(function ($q) use ($user) {
        $q->where('sender_id', auth()->id())
          ->where('receiver_id', $user->id);
    })->orWhere(function ($q) use ($user) {
        $q->where('sender_id', $user->id)
          ->where('receiver_id', auth()->id());
    })->orderBy('created_at')->get();

    return view('messages.chat', compact('user', 'messages'));
}

}
