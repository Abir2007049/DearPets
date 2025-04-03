<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="container py-5">
        <!-- User Profile Section -->
        <div class="card p-4 mb-4 rounded-3 border-0 shadow-sm bg-light">
            <h2 class="card-title text-center mb-4 text-primary">{{ $user->name }}'s Profile</h2>
            <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="card-text"><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
        </div>

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
