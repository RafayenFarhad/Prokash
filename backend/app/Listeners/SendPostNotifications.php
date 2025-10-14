<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $post = $event->post;
        
        // Get all users except the post author
        $users = User::where('id', '!=', $post->user_id)
                     ->where('is_active', true)
                     ->get();

        // Determine notification type and message based on priority
        $isEmergency = in_array($post->priority, ['emergency', 'high']);
        
        $title = $isEmergency 
            ? '🚨 ' . strtoupper($post->priority) . ' ALERT!' 
            : 'New Alert Posted';
            
        $message = $isEmergency
            ? "URGENT: {$post->title} - {$post->category->name}"
            : "{$post->user->name} posted: {$post->title}";

        // Create notifications for all users
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $isEmergency ? 'emergency_alert' : 'new_post',
                'title' => $title,
                'message' => $message,
                'data' => json_encode([
                    'post_id' => $post->id,
                    'post_title' => $post->title,
                    'priority' => $post->priority,
                    'category' => $post->category->name ?? 'Uncategorized',
                    'location' => $post->location_name,
                ]),
                'read' => false,
            ]);
        }
    }
}
