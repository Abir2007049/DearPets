@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Your Inbox</h2>

    @if($latestMessages->isEmpty())
        <p class="text-muted">You have no messages yet.</p>
    @else
        @foreach($latestMessages as $msg)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $msg->sender->name }}</h5>
                    <p class="card-text">{{ $msg->content }}</p>
                    <small class="text-muted">Sent at {{ $msg->created_at->format('M d, Y h:i A') }}</small>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
