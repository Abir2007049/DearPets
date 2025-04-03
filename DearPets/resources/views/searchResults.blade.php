<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Search Results for "{{ $query }}"</h2>
        @forelse($users as $user)
            <div class="p-4 bg-white rounded-xl shadow mb-4">
                <a href="{{ route('user.profile', $user->id) }}" class="font-semibold text-xl hover:underline">
                    {{ $user->name }}
                </a>
                <p>Email: {{ $user->email }}</p>
            </div>
        @empty
            <p>No users found.</p>
        @endforelse
    </div>
</x-app-layout>
