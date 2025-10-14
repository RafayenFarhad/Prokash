<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read',
        'read_at',
        'post_id',
        'action_url',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected $appends = ['redirect_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the URL to redirect when notification is clicked
     */
    public function getRedirectUrlAttribute(): ?string
    {
        // If action_url is set, use it
        if ($this->action_url) {
            return $this->action_url;
        }

        // If post_id exists, redirect to post detail
        if ($this->post_id) {
            return "/posts/{$this->post_id}";
        }

        // Parse post_id from data if available
        if (isset($this->data['post_id'])) {
            return "/posts/{$this->data['post_id']}";
        }

        return null;
    }
}
