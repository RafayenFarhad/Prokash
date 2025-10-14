<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostVerification;
use App\Services\ReputationService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function toggle(Request $request, $postId)
    {
        $request->validate([
            'type' => 'required|in:upvote,downvote',
        ]);

        $post = Post::findOrFail($postId);
        $userId = $request->user()->id;

        $existing = PostVerification::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if ($existing) {
            if ($existing->type === $request->type) {
                $existing->delete();
                $this->updatePostScore($post);
                return response()->json(['message' => 'Verification removed']);
            }
            
            $existing->update(['type' => $request->type]);
            $this->updatePostScore($post);
            return response()->json(['message' => 'Verification updated']);
        }

        PostVerification::create([
            'user_id' => $userId,
            'post_id' => $postId,
            'type' => $request->type,
        ]);

        $this->updatePostScore($post);

        return response()->json(['message' => 'Verification added']);
    }

    public function check(Request $request, $postId)
    {
        $verification = PostVerification::where('user_id', $request->user()->id)
            ->where('post_id', $postId)
            ->first();

        return response()->json([
            'verified' => $verification ? true : false,
            'type' => $verification ? $verification->type : null,
        ]);
    }

    private function updatePostScore(Post $post)
    {
        $upvotes = $post->verifications()->where('type', 'upvote')->count();
        $downvotes = $post->verifications()->where('type', 'downvote')->count();
        
        $score = $upvotes - $downvotes;
        
        $wasVerified = $post->is_verified;
        
        $post->update([
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
            'verification_score' => $score,
            'is_verified' => $score >= 3, // Verified if 3+ net upvotes (lowered for better user experience)
        ]);

        if (!$wasVerified && $post->is_verified) {
            $reputationService = new ReputationService();
            $reputationService->triggerReputationUpdate($post->user, 'post_verified', [
                'post_id' => $post->id,
                'verification_score' => $score
            ]);
        }
    }
}
