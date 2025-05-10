<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="container py-5">
        <!-- User Profile Section -->
        <div class="card p-4 mb-4 rounded-3 border-0 shadow-sm bg-light">
            <h2 class="card-title text-center mb-4 text-primary">{{ $user->name }}'s Profile</h2>
            <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="card-text"><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>

            @if ($user->id !== Auth::id())
                @php
                    $friendship = \App\Models\Friendship::where(function ($q) use ($user) {
                        $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
                    })->orWhere(function ($q) use ($user) {
                        $q->where('receiver_id', Auth::id())->where('sender_id', $user->id);
                    })->first();
                @endphp

                <div class="mt-3">
                    @if (!$friendship)
                        <form action="{{ route('friend.request', $user->id) }}" method="POST" class="d-inline">@csrf
                            <button class="btn btn-success">Add Friend</button>
                        </form>
                    @elseif ($friendship->status == 'pending' && $friendship->sender_id == Auth::id())
                        <button class="btn btn-secondary" disabled>Request Sent</button>
                    @elseif ($friendship->status == 'pending' && $friendship->receiver_id == Auth::id())
                        <form action="{{ route('friend.accept', $friendship->id) }}" method="POST" class="d-inline">@csrf
                            <button class="btn btn-primary">Accept</button>
                        </form>
                        <form action="{{ route('friend.reject', $friendship->id) }}" method="POST" class="d-inline">@csrf
                            <button class="btn btn-danger">Reject</button>
                        </form>
                    @elseif ($friendship->status == 'accepted')
                        <form action="{{ route('friend.unfriend', $user->id) }}" method="POST" class="d-inline">@csrf
                            <button class="btn btn-outline-danger">Unfriend</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
        <!-- Friends Section -->
@if ($friends->count())
    <div class="card p-4 mb-4 rounded-3 border-0 shadow-sm bg-light">
        <h4 class="mb-3 text-primary">{{ $user->name }}'s Friends ({{ $friends->count() }})</h4>
        <ul class="list-group list-group-flush">
            @foreach ($friends as $friend)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('profile.show', $friend->id) }}" class="text-decoration-none text-dark">
                        {{ $friend->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="card p-4 mb-4 rounded-3 border-0 shadow-sm bg-light">
        <p class="text-muted mb-0">{{ $user->name }} has no friends yet.</p>
    </div>
@endif


        <!-- Posts Section -->
        <h3 class="text-center mb-4 text-success">Posts by {{ $user->name }}</h3>
        @forelse($user->posts as $post)
            <div class="card mb-4 rounded-3 border-0 shadow-sm bg-white">
                <div class="card-body">
                    <p class="card-text">{{ $post->caption }}</p>

                    @if ($post->image)
                        <img src="{{ asset('storage/posts/' . $post->image) }}" class="card-img-top rounded-3 w-100 object-fit-cover mt-2" alt="Post Image" style="max-height: 400px;">
                    @endif

                    @if ($post->video)
                        <video controls class="w-100 rounded-3 mt-2">
                            <source src="{{ asset('storage/' . $post->video) }}" type="video/mp4">
                        </video>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-muted">No posts yet.</p>
        @endforelse
    </div>
</x-app-layout>
