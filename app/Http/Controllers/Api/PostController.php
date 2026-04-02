<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * GET /api/posts
     * Returns paginated posts for the mobile app feed.
     */
    public function index(Request $request)
    {
        $posts = Post::with('tags:id,name')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $posts,
        ]);
    }

    /**
     * GET /api/posts/{id}
     * Returns a single post's full detail (mobile taps a notification).
     */
    public function show(Post $post)
    {
        $post->load('tags:id,name');

        return response()->json([
            'success' => true,
            'data'    => $post,
        ]);
    }
}
