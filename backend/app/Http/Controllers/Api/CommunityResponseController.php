<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityResponse;
use App\Models\Post;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommunityResponseController extends Controller
{
    /**
     * Offer help on a post
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'response_type' => 'required|in:help,resource,info,volunteer',
            'message' => 'required|string|max:500',
            'resources' => 'nullable|array',
            'contact_method' => 'nullable|in:phone,email,whatsapp',
            'contact_info' => 'nullable|string',
        ]);

        $post = Post::findOrFail($postId);

        // Check if user already offered help
        $existingResponse = CommunityResponse::where('post_id', $postId)
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        if ($existingResponse) {
            return response()->json([
                'message' => 'You have already offered help on this post',
            ], 409);
        }

        $response = CommunityResponse::create([
            'post_id' => $postId,
            'user_id' => $request->user()->id,
            'response_type' => $request->response_type,
            'message' => $request->message,
            'resources' => $request->resources,
            'contact_method' => $request->contact_method,
            'contact_info' => $request->contact_info,
        ]);

        // Notify post author
        Notification::create([
            'user_id' => $post->user_id,
            'type' => 'community_help',
            'title' => '🤝 Someone Offered Help!',
            'message' => "{$request->user()->name} offered to help with your alert: {$post->title}",
            'data' => json_encode([
                'response_id' => $response->id,
                'post_id' => $postId,
                'helper_name' => $request->user()->name,
                'response_type' => $request->response_type,
            ]),
        ]);

        return response()->json([
            'response' => $response->load('user'),
            'message' => 'Help offer submitted successfully',
        ], 201);
    }

    /**
     * Get all responses for a post
     */
    public function index($postId)
    {
        $responses = CommunityResponse::where('post_id', $postId)
            ->where('status', 'active')
            ->with('user:id,name,reputation_level,trust_score')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($responses);
    }

    /**
     * Update response status
     */
    public function update(Request $request, $postId, $responseId)
    {
        $request->validate([
            'status' => 'required|in:active,fulfilled,cancelled',
        ]);

        $response = CommunityResponse::where('id', $responseId)
            ->where('post_id', $postId)
            ->firstOrFail();

        $post = Post::findOrFail($postId);

        // Only post author or response creator can update
        if ($post->user_id !== $request->user()->id && $response->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $response->update(['status' => $request->status]);

        return response()->json([
            'response' => $response,
            'message' => 'Response status updated',
        ]);
    }

    /**
     * Get user's help offers
     */
    public function myOffers(Request $request)
    {
        $responses = CommunityResponse::where('user_id', $request->user()->id)
            ->with(['post:id,title,status', 'post.user:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($responses);
    }
}
