<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
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
}
