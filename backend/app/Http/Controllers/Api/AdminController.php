<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get public statistics for landing page
     */
    public function publicStats()
    {
        $totalPosts = Post::count();
        $totalUsers = User::where('is_active', true)->count();
        $totalComments = Comment::count();
        
        // Calculate verification rate
        $verifiedPosts = Post::where('is_verified', true)->count();
        $verificationRate = $totalPosts > 0 ? round(($verifiedPosts / $totalPosts) * 100) : 0;
        
        // Calculate average response time (in minutes)
        // Get posts with comments and calculate time difference
        $postsWithComments = Post::whereHas('comments')->with('comments')->get();
        $totalResponseTime = 0;
        $responseCount = 0;
        
        foreach ($postsWithComments as $post) {
            $firstComment = $post->comments->sortBy('created_at')->first();
            if ($firstComment) {
                $responseTime = $post->created_at->diffInMinutes($firstComment->created_at);
                $totalResponseTime += $responseTime;
                $responseCount++;
            }
        }
        
        $avgResponseTime = $responseCount > 0 ? round($totalResponseTime / $responseCount) : 2;

        $stats = [
            'total_users' => $totalUsers,
            'total_posts' => $totalPosts,
            'total_comments' => $totalComments,
            'verification_rate' => $verificationRate,
            'avg_response_time' => $avgResponseTime,
        ];

        return response()->json($stats);
    }

    /**
     * Get dashboard statistics (Admin only)
     */
    public function stats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'verified_posts' => Post::where('is_verified', true)->count(),
            'emergency_posts' => Post::where('priority', 'emergency')->count(),
            'total_comments' => Comment::count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_posts' => Post::with('user')->latest()->take(5)->get(),
            'posts_this_month' => Post::whereMonth('created_at', now()->month)->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get all users with pagination
     */
    public function users(Request $request)
    {
        $query = User::withCount(['posts', 'comments']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(20);

        return response()->json($users);
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user = User::findOrFail($userId);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'user' => $user,
            'message' => 'User role updated successfully',
        ]);
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'user' => $user,
            'message' => 'User status updated successfully',
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Get all posts for moderation
     */
    public function posts(Request $request)
    {
        $query = Post::with(['user', 'category'])
            ->withCount(['likes', 'comments']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->latest()->paginate(20);

        return response()->json($posts);
    }

    /**
     * Delete any post (admin moderation)
     */
    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }

    /**
     * Admin manual verification toggle
     * Allows admins to verify/unverify posts regardless of community votes
     */
    public function togglePostVerification(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        
        // Toggle verification status
        $newStatus = !$post->is_verified;
        
        $post->update([
            'is_verified' => $newStatus,
            'admin_verified' => $newStatus, // Track that admin manually verified this
            'admin_verified_by' => $newStatus ? auth()->id() : null,
            'admin_verified_at' => $newStatus ? now() : null,
        ]);

        // If admin is verifying the post, trigger reputation update and notification
        if ($newStatus && !$post->wasChanged('is_verified')) {
            $reputationService = new \App\Services\ReputationService();
            $reputationService->triggerReputationUpdate($post->user, 'post_verified', [
                'post_id' => $post->id,
                'verification_score' => $post->verification_score,
                'admin_verified' => true
            ]);

            // Send notification to post author
            $notificationService = new NotificationService();
            $notificationService->notifyPostVerified($post, true);
        }

        return response()->json([
            'message' => $newStatus ? 'Post verified successfully by admin' : 'Post verification removed',
            'post' => $post->fresh(['user', 'category']),
        ]);
    }

    /**
     * Get verification statistics for admin dashboard
     */
    public function verificationStats()
    {
        $totalPosts = Post::count();
        $verifiedPosts = Post::where('is_verified', true)->count();
        $adminVerified = Post::where('admin_verified', true)->count();
        $communityVerified = Post::where('is_verified', true)
            ->where(function($q) {
                $q->whereNull('admin_verified')
                  ->orWhere('admin_verified', false);
            })->count();
        
        $pendingVerification = Post::where('is_verified', false)
            ->where('verification_score', '>=', 2) // Close to verification threshold
            ->count();

        return response()->json([
            'total_posts' => $totalPosts,
            'verified_posts' => $verifiedPosts,
            'admin_verified' => $adminVerified,
            'community_verified' => $communityVerified,
            'pending_verification' => $pendingVerification,
            'verification_rate' => $totalPosts > 0 ? round(($verifiedPosts / $totalPosts) * 100, 1) : 0,
        ]);
    }
}
