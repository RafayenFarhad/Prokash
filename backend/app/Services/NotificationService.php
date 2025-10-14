<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Post;

class NotificationService
{
    /**
     * Send notification to all users (for emergency alerts)
     */
    public function notifyAllUsers(Post $post, string $type = 'emergency_alert')
    {
        $users = User::where('is_active', true)
            ->where('id', '!=', $post->user_id) // Don't notify the post creator
            ->get();

        foreach ($users as $user) {
            $this->createNotification($user->id, $type, $post);
        }

        return $users->count();
    }

    /**
     * Send notification to nearby users (based on location)
     */
    public function notifyNearbyUsers(Post $post, float $radiusKm = 10)
    {
        if (!$post->latitude || !$post->longitude) {
            return 0;
        }

        // Find users within radius
        $users = User::where('is_active', true)
            ->where('id', '!=', $post->user_id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", 
                [$post->latitude, $post->longitude, $post->latitude])
            ->having('distance', '<=', $radiusKm)
            ->get();

        foreach ($users as $user) {
            $this->createNotification($user->id, 'nearby_alert', $post);
        }

        return $users->count();
    }

    /**
     * Notify users when their post gets verified
     */
    public function notifyPostVerified(Post $post, bool $byAdmin = false)
    {
        $type = $byAdmin ? 'post_verified_admin' : 'post_verified_community';
        
        $this->createNotification(
            $post->user_id,
            $type,
            $post,
            $byAdmin ? 'Your post has been verified by admin' : 'Your post has been verified by the community'
        );
    }

    /**
     * Notify post author when someone comments
     */
    public function notifyNewComment(Post $post, $commentUser)
    {
        if ($post->user_id === $commentUser->id) {
            return; // Don't notify if commenting on own post
        }

        $this->createNotification(
            $post->user_id,
            'new_comment',
            $post,
            "{$commentUser->name} commented on your post"
        );
    }

    /**
     * Notify post author when someone likes their post
     */
    public function notifyPostLiked(Post $post, $likerUser)
    {
        if ($post->user_id === $likerUser->id) {
            return; // Don't notify if liking own post
        }

        $this->createNotification(
            $post->user_id,
            'post_liked',
            $post,
            "{$likerUser->name} liked your post"
        );
    }

    /**
     * Notify users when a post is updated
     */
    public function notifyPostUpdate(Post $post)
    {
        // Notify users who have interacted with the post
        $interestedUserIds = collect()
            ->merge($post->likes()->pluck('user_id'))
            ->merge($post->comments()->pluck('user_id'))
            ->unique()
            ->filter(fn($id) => $id !== $post->user_id)
            ->values();

        foreach ($interestedUserIds as $userId) {
            $this->createNotification(
                $userId,
                'post_updated',
                $post,
                "A post you're following has been updated"
            );
        }

        return $interestedUserIds->count();
    }

    /**
     * Create a notification
     */
    private function createNotification(int $userId, string $type, Post $post, ?string $customMessage = null)
    {
        $messages = [
            'emergency_alert' => "🚨 EMERGENCY: {$post->title}",
            'nearby_alert' => "📍 New alert near you: {$post->title}",
            'post_verified_admin' => "✅ Your post '{$post->title}' has been verified by admin",
            'post_verified_community' => "✅ Your post '{$post->title}' has been verified by the community",
            'new_comment' => "💬 New comment on '{$post->title}'",
            'post_liked' => "❤️ Someone liked your post '{$post->title}'",
            'post_updated' => "📝 Update: {$post->title}",
            'high_priority_alert' => "⚠️ HIGH PRIORITY: {$post->title}",
        ];

        $title = $this->getNotificationTitle($type, $post);
        $message = $customMessage ?? ($messages[$type] ?? "New notification about: {$post->title}");

        Notification::create([
            'user_id' => $userId,
            'post_id' => $post->id, // Link to post for cascade delete
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => "/posts/{$post->id}", // URL to redirect when clicked
            'data' => json_encode([
                'post_id' => $post->id,
                'post_title' => $post->title,
                'post_priority' => $post->priority,
                'post_category' => $post->category?->name,
                'location' => $post->location_name,
            ]),
            'read' => false,
        ]);
    }

    /**
     * Get notification title based on type
     */
    private function getNotificationTitle(string $type, Post $post): string
    {
        $titles = [
            'emergency_alert' => '🚨 Emergency Alert',
            'nearby_alert' => '📍 Nearby Alert',
            'post_verified_admin' => '✅ Post Verified',
            'post_verified_community' => '✅ Community Verified',
            'new_comment' => '💬 New Comment',
            'post_liked' => '❤️ Post Liked',
            'post_updated' => '📝 Post Updated',
            'high_priority_alert' => '⚠️ High Priority Alert',
        ];

        return $titles[$type] ?? 'New Notification';
    }

    /**
     * Send notification based on post priority
     */
    public function notifyBasedOnPriority(Post $post)
    {
        $notifiedCount = 0;

        switch ($post->priority) {
            case 'emergency':
                // Notify ALL users for emergency
                $notifiedCount = $this->notifyAllUsers($post, 'emergency_alert');
                break;

            case 'high':
                // Notify users within 20km for high priority
                $notifiedCount = $this->notifyNearbyUsers($post, 20);
                if ($notifiedCount === 0) {
                    // If no nearby users, notify all
                    $notifiedCount = $this->notifyAllUsers($post, 'high_priority_alert');
                }
                break;

            case 'medium':
                // Notify users within 10km for medium priority
                $notifiedCount = $this->notifyNearbyUsers($post, 10);
                break;

            case 'low':
                // Notify users within 5km for low priority
                $notifiedCount = $this->notifyNearbyUsers($post, 5);
                break;
        }

        return $notifiedCount;
    }
}
