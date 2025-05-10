<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Friend Requests</h2>
        @forelse($requests as $request)
            <div class="p-4 bg-white rounded-xl shadow mb-4">
                <p><strong>{{ $request->sender->name }}</strong> sent you a friend request.</p>
                <form action="{{ route('friend.accept', $request->id) }}" method="POST" class="inline">@csrf
                    <button class="btn btn-primary">Accept</button>
                </form>
                <form action="{{ route('friend.reject', $request->id) }}" method="POST" class="inline">@csrf
                    <button class="btn btn-danger">Reject</button>
                </form>
            </div>
        @empty
            <p>No pending requests.</p>
        @endforelse
    </div>
</x-app-layout>
