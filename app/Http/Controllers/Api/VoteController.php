<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostVote;
use App\Services\ReputationService;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function vote(Request $request, $postId)
    {
        $request->validate([
            'vote_type' => 'required|in:upvote,downvote',
        ]);

        $post = Post::findOrFail($postId);
        $userId = $request->user()->id;

        $existingVote = PostVote::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $request->vote_type) {
                $existingVote->delete();
                $message = 'Vote removed';
            } else {
                $existingVote->update(['vote_type' => $request->vote_type]);
                $message = 'Vote updated';
            }
        } else {
            PostVote::create([
                'user_id' => $userId,
                'post_id' => $postId,
                'vote_type' => $request->vote_type,
            ]);
            $message = 'Vote added';
        }

        $upvotes = PostVote::where('post_id', $postId)->where('vote_type', 'upvote')->count();
        $downvotes = PostVote::where('post_id', $postId)->where('vote_type', 'downvote')->count();

        $post->update([
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
        ]);

        $reputationService = new ReputationService();
        $reputationService->triggerReputationUpdate($post->user, 'vote_received', [
            'post_id' => $postId,
            'vote_type' => $request->vote_type,
            'voter_id' => $userId
        ]);

        return response()->json([
            'message' => $message,
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
            'user_vote' => PostVote::where('user_id', $userId)->where('post_id', $postId)->first()?->vote_type,
        ]);
    }

    public function getUserVote(Request $request, $postId)
    {
        $vote = PostVote::where('user_id', $request->user()->id)
            ->where('post_id', $postId)
            ->first();

        return response()->json([
            'vote_type' => $vote?->vote_type,
        ]);
    }
}
