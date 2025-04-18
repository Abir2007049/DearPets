@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Your Messages</h2>

    @if($conversations->isEmpty())
        <p>No messages yet.</p>
    @else
        @foreach($conversations as $conversation)
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title">
                        Conversation with {{ $conversation['user']->name }}
                    </h5>
                    <p class="card-text">
                        {{ $conversation['last_message']->content }}
                        <br>
                        <small class="text-muted">
                            {{ $conversation['last_message']->created_at->diffForHumans() }}
                        </small>
                    </p>
                    <a href="{{ route('chat.show', $conversation['user']->id) }}" class="btn btn-primary">
                        View Conversation
                    </a>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
