<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyBroadcast extends Model
{
    protected $fillable = [
        'admin_id',
        'title',
        'message',
        'severity',
        'target_area',
        'target_categories',
        'send_email',
        'send_sms', 
        'send_push',
        'recipients_count',
        'sent_at'
    ];

    protected $casts = [
        'target_categories' => 'array',
        'send_email' => 'boolean',
        'send_sms' => 'boolean',
        'send_push' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
