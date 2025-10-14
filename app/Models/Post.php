<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'image',
        'video',
        'latitude',
        'longitude',
        'location_name',
        'priority',
        'status',
        'resolved_at',
        'is_verified',
        'verification_score',
        'upvotes',
        'downvotes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected $appends = ['likes_count', 'comments_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function verifications()
    {
        return $this->hasMany(PostVerification::class);
    }

    public function reports()
    {
        return $this->hasMany(PostReport::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    public function updates()
    {
        return $this->hasMany(PostUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * Accessors
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }
}
