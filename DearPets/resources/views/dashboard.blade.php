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
        .chat-bubble {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 10px;
            max-width: 80%;
        }
        .text-end {
            margin-left: auto;
        }
        .chat-wrapper {
            height: 200px;
            overflow-y: scroll;
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
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
                                    <form id="likeForm_{{ $post->id }}" action="{{ route('post.favorite', $post->id) }}" method="POST">
                                        @csrf
                                        <button id="likeBtn_{{ $post->id }}" type="submit" class="btn {{ $post->isLikedBy(Auth::user()) ? 'btn-danger' : 'btn-outline-danger' }}">
                                            ❤️ Like
                                        </button>
                                    </form>
                                </div>

                                <button id="toggleCommentsBtn_{{ $post->id }}" class="btn btn-info mt-2 w-100">
                                    Show Comments
                                </button>

                                <div id="commentsSection_{{ $post->id }}" class="comments-section" style="display: none;">
                                    @foreach ($post->comments as $comment)
                                        <div class="bg-light p-3 rounded mt-2">
                                            <p class="text-dark"><strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}</p>
                                        </div>
                                    @endforeach

                                    <form action="{{ route('post.comment', $post->id) }}" method="POST" class="mt-3">
                                        @csrf
                                        <div class="d-flex gap-2">
                                            <input type="text" name="content" placeholder="Write a comment..." class="form-control">
                                            <button type="submit" class="btn btn-primary">Comment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Search & Chat Sidebar -->
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Search Users</h5>
                    <form action="{{ route('user.search') }}" method="GET">
                        <input type="text" name="query" class="form-control" placeholder="Search users by name">
                        <button type="submit" class="btn btn-info mt-2 w-100">Search</button>
                    </form>
                </div>

                <div class="card p-3 mt-3">
                    <a href="{{ route('messages.inbox') }}" class="btn btn-outline-primary mt-2 w-100">📥 Inbox</a>

                    <h5>Chat</h5>
                    <select id="chatUser" class="form-select mb-2">
                        <option value="" selected disabled>Select a user</option>
                        @foreach ($users as $user)
                            @if ($user->id !== Auth::id())
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>

                    <div id="chatMessages" class="chat-wrapper mb-2"></div>

                    <div class="input-group">
                        <input type="text" id="chatInput" class="form-control" placeholder="Type a message">
                        <button class="btn btn-primary" id="sendBtn">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        let selectedUserId = null;

        document.querySelectorAll('[id^="likeBtn_"]').forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();
                const postId = this.id.split("_")[1];
                const form = document.getElementById("likeForm_" + postId);

                fetch(form.action, {
                    method: "POST",
                    body: new FormData(form),
                    headers: { "X-CSRF-TOKEN": csrfToken },
                })
                .then(res => res.json())
                .then(data => {
                    const likeBtn = document.getElementById("likeBtn_" + postId);
                    likeBtn.className = 'btn ' + (data.liked ? 'btn-danger' : 'btn-outline-danger');
                    likeBtn.textContent = data.liked ? "❤️ Liked" : "🤍 Like";
                })
                .catch(err => console.error("Like error:", err));
            });
        });

        document.querySelectorAll('[id^="toggleCommentsBtn_"]').forEach(button => {
            button.addEventListener("click", function () {
                const postId = this.id.split("_")[1];
                const section = document.getElementById("commentsSection_" + postId);
                const show = section.style.display === "none" || section.style.display === "";
                section.style.display = show ? "block" : "none";
                this.textContent = show ? "Hide Comments" : "Show Comments";
            });
        });

        // Chat Events
        document.getElementById("chatUser").addEventListener("change", function () {
            selectedUserId = this.value;
            fetchMessages();
        });

        document.getElementById("sendBtn").addEventListener("click", function () {
            const input = document.getElementById("chatInput");
            const message = input.value.trim();
            if (!message || !selectedUserId) return;

            fetch('/messages/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    receiver_id: selectedUserId,
                    content: message
                })
            })
            .then(res => res.json())
            .then(() => {
                input.value = '';
                fetchMessages();
            })
            .catch(err => console.error("Message send error:", err));
        });

        function fetchMessages() {
            if (!selectedUserId) return;

            fetch(`/messages/${selectedUserId}`)
            .then(res => res.json())
            .then(messages => renderChat(messages))
            .catch(err => console.error("Fetch error:", err));
        }

        function renderChat(messages) {
            const chatBox = document.getElementById("chatMessages");
            chatBox.innerHTML = '';
            messages.forEach(msg => {
                const bubble = document.createElement("div");
                bubble.className = "chat-bubble " + (msg.sender_id == {{ Auth::id() }} ? "bg-primary text-white text-end" : "bg-light text-dark");
                bubble.textContent = msg.content;
                chatBox.appendChild(bubble);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
    </script>
</body>
</html>
