<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of tags
     */
    public function index()
    {
        $tags = Tag::withCount('posts')->get();
        return response()->json($tags);
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'tag' => $tag,
            'message' => 'Tag created successfully',
        ], 201);
    }

    /**
     * Display the specified tag
     */
    public function show($id)
    {
        $tag = Tag::withCount('posts')->findOrFail($id);
        return response()->json($tag);
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:tags,name,' . $id,
        ]);

        if ($request->has('name')) {
            $tag->name = $request->name;
            $tag->slug = Str::slug($request->name);
        }

        $tag->save();

        return response()->json([
            'tag' => $tag,
            'message' => 'Tag updated successfully',
        ]);
    }

    /**
     * Remove the specified tag
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
        ]);
    }
}
