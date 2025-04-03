<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  // Foreign key to the user table
        'caption',  // Text content of the post
        'image',    // Path to the uploaded image
        'video',    // Path to the uploaded video
    ];

    // Relationship with the User model (each post belongs to one user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the many-to-many relationship for likes
    public function likes()
    {
        return $this->belongsToMany(User::class, 'favorites', 'post_id', 'user_id');
    }

    // Relationship with comments (each post can have many comments)
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Check if a post is liked by the authenticated user
    public function isLikedBy($user)
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
