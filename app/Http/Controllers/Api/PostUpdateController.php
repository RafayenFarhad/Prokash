<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostUpdate;
use Illuminate\Http\Request;

class PostUpdateController extends Controller
{
    /**
     * Add update to a post
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
            'status_change' => 'nullable|in:ongoing,resolved,false_alarm',
        ]);

        $post = Post::findOrFail($postId);
        
        // Only author or admin can update
        if ($post->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $update = PostUpdate::create([
            'post_id' => $postId,
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'status_change' => $request->status_change,
        ]);

        // Update post status
        if ($request->status_change) {
            $post->update([
                'status' => $request->status_change,
                'resolved_at' => $request->status_change === 'resolved' ? now() : null,
            ]);
        }

        return response()->json([
            'update' => $update->load('user'),
            'message' => 'Update added successfully',
        ]);
    }

    /**
     * Get all updates for a post
     */
    public function index($postId)
    {
        $updates = PostUpdate::where('post_id', $postId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($updates);
    }
}
