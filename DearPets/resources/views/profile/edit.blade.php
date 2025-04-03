<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="container py-5">
        <div class="card p-4 shadow-sm rounded-3">
            <h2 class="card-title text-center mb-4">Edit Profile</h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Picture:</label>
                    @if($user->profile_picture)
                        <div class="mb-2">
                            <img src="{{ asset('storage/profile/' . $user->profile_picture) }}" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="profile_picture" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Profile</button>
            </form>
        </div>
    </div>
</x-app-layout>
