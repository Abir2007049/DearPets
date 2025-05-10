<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',  // ✅ Ensures password is automatically hashed
        'email_verified_at' => 'datetime',
    ];
    public function posts()
{
    return $this->hasMany(Post::class);
}
// app/Models/User.php

public function sentFriendRequests()
{
    return $this->hasMany(Friendship::class, 'sender_id');
}

public function receivedFriendRequests()
{
    return $this->hasMany(Friendship::class, 'receiver_id');
}

public function friends()
{
    return User::whereHas('sentFriendRequests', function ($q) {
        $q->where('receiver_id', $this->id)->where('status', 'accepted');
    })->orWhereHas('receivedFriendRequests', function ($q) {
        $q->where('sender_id', $this->id)->where('status', 'accepted');
    });
}


}
