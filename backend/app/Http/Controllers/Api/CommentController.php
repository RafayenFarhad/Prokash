<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Services\ReputationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($postId)
    {
        $comments = Comment::where('post_id', $postId)
            ->whereNull('parent_id') // Only get top-level comments
            ->with(['user', 'replies.user', 'replies.replies.user']) // Load nested replies
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $post = Post::findOrFail($postId);

        if ($request->parent_id) {
            $parentComment = Comment::where('id', $request->parent_id)
                ->where('post_id', $postId)
                ->firstOrFail();
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        $comment->load(['user', 'parent.user']);

        $reputationService = new ReputationService();
        $reputationService->triggerReputationUpdate($request->user(), 'comment_created', [
            'comment_id' => $comment->id,
            'post_id' => $post->id,
            'is_reply' => !is_null($request->parent_id)
        ]);

        // Send notification to post author
        $notificationService = new NotificationService();
        $notificationService->notifyNewComment($post, $request->user());

        return response()->json([
            'comment' => $comment,
            'message' => $request->parent_id ? 'Reply added successfully' : 'Comment added successfully',
        ], 201);
    }

    public function update(Request $request, $postId, $id)
    {
        $comment = Comment::where('post_id', $postId)->findOrFail($id);

        // Check authorization
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        $comment->load('user');

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment updated successfully',
        ]);
    }

    public function destroy(Request $request, $postId, $id)
    {
        $comment = Comment::where('post_id', $postId)->findOrFail($id);

        // Check authorization
        if ($comment->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
