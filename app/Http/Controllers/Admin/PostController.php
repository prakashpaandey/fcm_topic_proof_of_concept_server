<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Post;
use App\Services\NotificationTargetingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct(protected NotificationTargetingService $notifier) {}

    public function index()
    {
        $posts = Post::with('tags', 'author')->latest()->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $interests = Interest::orderBy('name')->get();
        return view('admin.posts.create', compact('interests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'text_content' => 'required|string',
            'media_url'    => 'nullable|url',
            'tag_ids'      => 'nullable|array',
            'tag_ids.*'    => 'integer|exists:interests,id',
        ]);

        $post = Post::create([
            'admin_id'     => Auth::id(),
            'title'        => $request->title,
            'text_content' => $request->text_content,
            'media_url'    => $request->media_url,
        ]);

        if ($request->filled('tag_ids')) {
            $post->tags()->sync($request->tag_ids);
        }

        // Trigger interest-based push notifications
        $this->notifier->dispatchForPost($post);

        return redirect()->route('admin.posts.index')->with('success', 'Post published and notifications dispatched!');
    }

    public function show(Post $post)
    {
        $post->load('tags', 'author', 'notificationLogs.user');
        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $interests  = Interest::orderBy('name')->get();
        $selectedIds = $post->tags->pluck('id')->toArray();
        return view('admin.posts.edit', compact('post', 'interests', 'selectedIds'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'text_content' => 'required|string',
            'media_url'    => 'nullable|url',
            'tag_ids'      => 'nullable|array',
            'tag_ids.*'    => 'integer|exists:interests,id',
        ]);

        $post->update([
            'title'        => $request->title,
            'text_content' => $request->text_content,
            'media_url'    => $request->media_url,
        ]);

        $post->tags()->sync($request->tag_ids ?? []);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:posts,id',
        ]);

        Post::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.posts.index')->with('success', count($request->ids) . ' posts deleted.');
    }
}

