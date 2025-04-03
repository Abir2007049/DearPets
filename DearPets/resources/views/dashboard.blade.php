<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media - Pet Lovers</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 3px solid #ff69b4;
        }
        .card {
            border-radius: 15px;
        }
        .post-img {
            max-height: 400px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-md-3">
                <div class="card p-3 text-center">
                    <img src="{{ Auth::user()->profile_picture ? asset('storage/profile/' . Auth::user()->profile_picture) : asset('default-profile.png') }}" alt="Profile Picture" class="profile-pic rounded-circle mx-auto">
                    <h5 class="mt-3">{{ Auth::user()->name }}</h5>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">Edit Profile</a>
                    <a href="{{ route('profile.view', Auth::user()->id) }}" class="mt-2 d-block text-decoration-none">View Profile</a>
                </div>
            </div>

            <!-- Main Feed -->
            <div class="col-md-6">
                <div class="card p-4 mb-3">
                    <h4 class="mb-3">What's on your mind?</h4>
                    <form action="{{ route('post.create') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea name="caption" class="form-control mb-2" placeholder="Write something..."></textarea>
                        <input type="file" name="image" class="form-control mb-2">
                        <input type="file" name="video" class="form-control mb-2">
                        <button type="submit" class="btn btn-success w-100">Post</button>
                    </form>
                </div>

                <!-- Posts Section -->
                <h3 class="mb-3">Your Posts</h3>
                @if ($posts->isEmpty())
                    <p class="text-muted">No posts found.</p>
                @else
                    @foreach ($posts as $post)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="default-profile.png" alt="User" class="profile-pic rounded-circle me-2">
                                    <h6 class="m-0">{{ $post->user->name }}</h6>
                                </div>
                                <p class="mt-3">{{ $post->caption }}</p>
                                @if ($post->image)
                                    <img src="{{ asset('storage/posts/' . $post->image) }}" class="img-fluid post-img rounded mb-2">
                                @endif
                                @if ($post->video)
                                    <video controls class="w-100 rounded mb-2">
                                        <source src="{{ asset('storage/' . $post->video) }}" type="video/mp4">
                                    </video>
                                @endif

                                <div class="d-flex justify-content-between">
                                    <!-- Like Button -->
                                    <form id="likeForm_{{ $post->id }}" action="{{ route('post.favorite', $post->id) }}" method="POST">
                                        @csrf
                                        <button id="likeBtn_{{ $post->id }}" type="submit" class="btn {{ $post->isLikedBy(Auth::user()) ? 'btn-danger' : 'btn-outline-danger' }}">
                                            ❤️ Like
                                        </button>
                                    </form>
                                </div>

                                <!-- Toggle Comments Button -->
                                <button id="toggleCommentsBtn_{{ $post->id }}" class="btn btn-info mt-2 w-100">
                                    Show Comments
                                </button>

                                <!-- Comments Section -->
                                <div id="commentsSection_{{ $post->id }}" class="comments-section" style="display: none;">
                                    @foreach ($post->comments as $comment)
                                        <div class="bg-light p-3 rounded-lg mt-2">
                                            <p class="text-sm text-dark"><strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}</p>
                                        </div>
                                    @endforeach

                                    <!-- Add Comment Form -->
                                    <form action="{{ route('post.comment', $post->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        <div class="d-flex gap-2">
                                            <input type="text" name="content" placeholder="Write a comment..." class="form-control">
                                            <button type="submit" class="btn btn-primary">
                                                Comment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Search Sidebar -->
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Search Users</h5>
                    <form action="{{ route('user.search') }}" method="GET">
                        <input type="text" name="query" class="form-control" placeholder="Search users by name">
                        <button type="submit" class="btn btn-info mt-2 w-100">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Like Button Toggle -->
    <script>
    document.querySelectorAll('[id^="likeBtn_"]').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            var postId = this.id.split('_')[1]; 
            var form = document.getElementById('likeForm_' + postId);
            var likeButton = document.getElementById('likeBtn_' + postId);

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.liked) {
                    likeButton.classList.remove('btn-outline-danger');
                    likeButton.classList.add('btn-danger');
                    likeButton.innerHTML = "❤️ Liked";  // Update text
                } else {
                    likeButton.classList.remove('btn-danger');
                    likeButton.classList.add('btn-outline-danger');
                    likeButton.innerHTML = "🤍 Like";  // Update text
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
    // Like Button Toggle
    document.querySelectorAll('[id^="likeBtn_"]').forEach(function (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            var postId = this.id.split("_")[1];
            var form = document.getElementById("likeForm_" + postId);
            var likeButton = document.getElementById("likeBtn_" + postId);

            fetch(form.action, {
                method: "POST",
                body: new FormData(form),
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.liked) {
                        likeButton.classList.remove("btn-outline-danger");
                        likeButton.classList.add("btn-danger");
                        likeButton.innerHTML = "❤️ Liked"; // Change button text
                    } else {
                        likeButton.classList.remove("btn-danger");
                        likeButton.classList.add("btn-outline-danger");
                        likeButton.innerHTML = "🤍 Like"; // Change button text
                    }
                })
                .catch((error) => console.error("Error:", error));
        });
    });

    // Toggle Comments Section
    document.querySelectorAll('[id^="toggleCommentsBtn_"]').forEach(function (button) {
        button.addEventListener("click", function () {
            var postId = this.id.split("_")[1];
            var commentsSection = document.getElementById("commentsSection_" + postId);

            if (commentsSection.style.display === "none" || commentsSection.style.display === "") {
                commentsSection.style.display = "block";
                this.textContent = "Hide Comments"; 
            } else {
                commentsSection.style.display = "none";
                this.textContent = "Show Comments"; 
            }
        });
    });
});

</script>

</body>
</html>
