<?php

namespace App\Mail;

use App\Models\EmergencyBroadcast;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmergencyAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $broadcast;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(EmergencyBroadcast $broadcast, User $user)
    {
        $this->broadcast = $broadcast;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $severityColors = [
            'low' => '#3B82F6',      // Blue
            'medium' => '#F59E0B',   // Yellow
            'high' => '#EF4444',     // Red
            'critical' => '#DC2626'  // Dark Red
        ];

        return $this->subject('🚨 Emergency Alert: ' . $this->broadcast->title)
                    ->view('emails.emergency-alert')
                    ->with([
                        'broadcast' => $this->broadcast,
                        'user' => $this->user,
                        'severityColor' => $severityColors[$this->broadcast->severity] ?? '#6B7280',
                        'appUrl' => config('app.url'),
                    ]);
    }
}
