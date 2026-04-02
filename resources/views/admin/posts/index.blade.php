@extends('admin.layout')
@section('title', 'Posts')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-newspaper" style="color:#f59e0b;margin-right:10px"></i>Posts</h1>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create Post</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Tags</th>
                <th>Author</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($posts as $post)
            <tr>
                <td style="color:var(--muted)">{{ $post->id }}</td>
                <td>
                    <a href="{{ route('admin.posts.show', $post) }}" style="color:var(--accent2);text-decoration:none;font-weight:500">
                        {{ Str::limit($post->title, 50) }}
                    </a>
                </td>
                <td>
                    @foreach($post->tags as $tag)
                        <span class="badge badge-accent">{{ $tag->name }}</span>
                    @endforeach
                    @if($post->tags->isEmpty()) <span style="color:var(--muted);font-size:.8rem">—</span> @endif
                </td>
                <td style="color:var(--muted)">{{ $post->author->username ?? '—' }}</td>
                <td style="color:var(--muted);font-size:.8rem">{{ $post->created_at->format('d M Y, H:i') }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-eye"></i></a>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i></a>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:32px">No posts yet. <a href="{{ route('admin.posts.create') }}" style="color:var(--accent2)">Create one</a>.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px 16px">{{ $posts->links('pagination.custom') }}</div>
</div>
@endsection
