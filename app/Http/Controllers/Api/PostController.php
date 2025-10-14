<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostImage;
use App\Events\PostCreated;
use App\Services\AIService;
use App\Services\ReputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category', 'tags', 'comments.user', 'likes', 'images', 'updates'])
            ->withCount(['likes', 'comments']);

        // Search by keyword
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by tag
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter verified posts only
        if ($request->has('verified') && $request->verified) {
            $query->where('is_verified', true);
        }

        // Filter posts with location
        if ($request->has('has_location') && $request->has_location) {
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        }

        // Location-based filtering (proximity)
        if ($request->has('latitude') && $request->has('longitude') && $request->has('radius')) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius; // in kilometers

            // Haversine formula for distance calculation
            $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
                ->having('distance', '<=', $radius)
                ->orderBy('distance');
        } else {
            // Sort by priority and time
            $query->orderByRaw("FIELD(priority, 'emergency', 'high', 'medium', 'low')")
                ->latest();
        }

        $posts = $query->paginate(12);

        return response()->json($posts);
    }

    public function store(Request $request, AIService $aiService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:10240',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high,emergency',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $data = $request->except(['image', 'images', 'video', 'tags']);
        $data['user_id'] = $request->user()->id;
        $data['priority'] = $request->priority ?? 'low';
        $data['status'] = 'ongoing';

        if (!$data['category_id']) {
            $suggestedCategory = $aiService->categorizePost($request->title, $request->content);
            if ($suggestedCategory) {
                $data['category_id'] = $suggestedCategory;
            }
        }

        // Handle single image upload (backward compatibility)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $data['image'] = $path;
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('videos', 'public');
            $data['video'] = $path;
        }

        $post = Post::create($data);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('posts', 'public');
                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => $path,
                    'order' => $index,
                ]);
            }
        }

        // Attach tags
        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

        $post->load(['user', 'category', 'tags', 'images']);

        event(new PostCreated($post));

        $reputationService = new ReputationService();
        $reputationService->triggerReputationUpdate($request->user(), 'post_created', [
            'post_id' => $post->id,
            'priority' => $post->priority
        ]);

        return response()->json([
            'post' => $post,
            'message' => 'Post created successfully',
        ], 201);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'category', 'tags', 'comments.user', 'likes'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($id);

        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Check authorization
        if ($post->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $data = $request->except(['image', 'tags']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $path = $request->file('image')->store('posts', 'public');
            $data['image'] = $path;
        }

        $post->update($data);

        // Sync tags
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        $post->load(['user', 'category', 'tags']);

        return response()->json([
            'post' => $post,
            'message' => 'Post updated successfully',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Check authorization
        if ($post->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete image
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}
