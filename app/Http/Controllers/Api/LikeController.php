<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle like on a post
     */
    public function toggle(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $userId = $request->user()->id;

        $like = Like::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->first();

        if ($like) {
            // Unlike
            $like->delete();
            return response()->json([
                'liked' => false,
                'likes_count' => $post->likes()->count(),
                'message' => 'Post unliked',
            ]);
        } else {
            // Like
            Like::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            return response()->json([
                'liked' => true,
                'likes_count' => $post->likes()->count(),
                'message' => 'Post liked',
            ]);
        }
    }

    /**
     * Check if user has liked a post
     */
    public function check(Request $request, $postId)
    {
        $liked = Like::where('user_id', $request->user()->id)
            ->where('post_id', $postId)
            ->exists();

        return response()->json([
            'liked' => $liked,
        ]);
    }
}
