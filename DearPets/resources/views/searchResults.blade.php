<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Search Results for "{{ $query }}"</h2>
        @forelse($users as $user)
            @if ($user->id !== Auth::id())
                <div class="p-4 bg-white rounded-xl shadow mb-4">
                    <a href="{{ route('user.profile', $user->id) }}" class="font-semibold text-xl hover:underline">
                        {{ $user->name }}
                    </a>
                    <p>Email: {{ $user->email }}</p>

                    {{-- Friendship logic --}}
                    @php
                        $friendship = \App\Models\Friendship::where(function ($q) use ($user) {
                            $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
                        })->orWhere(function ($q) use ($user) {
                            $q->where('receiver_id', Auth::id())->where('sender_id', $user->id);
                        })->first();
                    @endphp

                    @if (!$friendship)
                        <form action="{{ route('friend.request', $user->id) }}" method="POST" class="mt-2">@csrf
                            <button class="btn btn-success">Add Friend</button>
                        </form>
                    @elseif ($friendship->status == 'pending' && $friendship->sender_id == Auth::id())
                        <button class="btn btn-secondary mt-2" disabled>Request Sent</button>
                    @elseif ($friendship->status == 'pending' && $friendship->receiver_id == Auth::id())
                        <form action="{{ route('friend.accept', $friendship->id) }}" method="POST" class="mt-2 d-inline">@csrf
                            <button class="btn btn-primary">Accept</button>
                        </form>
                        <form action="{{ route('friend.reject', $friendship->id) }}" method="POST" class="mt-2 d-inline">@csrf
                            <button class="btn btn-danger">Reject</button>
                        </form>
                    @elseif ($friendship->status == 'accepted')
                        <form action="{{ route('friend.unfriend', $user->id) }}" method="POST" class="mt-2">@csrf
                            <button class="btn btn-outline-danger">Unfriend</button>
                        </form>
                    @endif
                </div>
            @endif
        @empty
            <p>No users found.</p>
        @endforelse
    </div>
</x-app-layout>
