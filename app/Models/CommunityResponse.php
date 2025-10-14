<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityResponse extends Model
{
    protected $fillable = [
        'post_id',
        'user_id', 
        'response_type',
        'message',
        'resources',
        'contact_method',
        'contact_info',
        'status'
    ];

    protected $casts = [
        'resources' => 'array',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
