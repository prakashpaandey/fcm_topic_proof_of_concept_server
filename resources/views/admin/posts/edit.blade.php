@extends('admin.layout')
@section('title', 'Edit Post')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-pen-to-square" style="color:#f59e0b;margin-right:10px"></i>Edit Post</h1>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:720px">
    <div class="card-header">Editing Post #{{ $post->id }}</div>
    <div style="padding:24px">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="title">Title <span style="color:#ef4444">*</span></label>
                <input id="title" type="text" name="title" value="{{ old('title', $post->title) }}" required>
                @error('title') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="text_content">Content <span style="color:#ef4444">*</span></label>
                <textarea id="text_content" name="text_content" rows="6">{{ old('text_content', $post->text_content) }}</textarea>
                @error('text_content') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="media_url">Media URL <span style="color:var(--muted)">(optional)</span></label>
                <input id="media_url" type="url" name="media_url" value="{{ old('media_url', $post->media_url) }}" placeholder="https://example.com/image.jpg">
                @error('media_url') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Interest Tags</label>
                <div class="checkbox-grid">
                    @foreach($interests as $interest)
                        <label class="checkbox-item">
                            <input type="checkbox" name="tag_ids[]" value="{{ $interest->id }}"
                                {{ in_array($interest->id, old('tag_ids', $selectedIds)) ? 'checked' : '' }}>
                            <span>{{ $interest->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tag_ids') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:12px;margin-bottom:20px;font-size:.82rem;color:var(--muted)">
                <i class="fa-solid fa-circle-info" style="color:#f59e0b;margin-right:6px"></i>
                Editing a post does not re-send notifications. Notifications were sent when the post was originally published.
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update Post</button>
        </form>
    </div>
</div>
@endsection
