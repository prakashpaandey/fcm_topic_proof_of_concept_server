@extends('admin.layout')
@section('title', 'Create Post')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-pen-nib" style="color:#f59e0b;margin-right:10px"></i>Create Post</h1>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:720px">
    <div class="card-header">
        <span>New Post</span>
        <span style="font-size:.78rem;color:var(--muted);font-weight:400">Publishing will automatically trigger FCM notifications to matching users</span>
    </div>
    <div style="padding:24px">
        <form action="{{ route('admin.posts.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Title <span style="color:#ef4444">*</span></label>
                <input id="title" type="text" name="title" value="{{ old('title') }}" placeholder="Post headline..." required>
                @error('title') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="text_content">Content <span style="color:#ef4444">*</span></label>
                <textarea id="text_content" name="text_content" rows="6" placeholder="Write the full content here...">{{ old('text_content') }}</textarea>
                @error('text_content') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="media_url">Media URL <span style="color:var(--muted)">(optional)</span></label>
                <input id="media_url" type="url" name="media_url" value="{{ old('media_url') }}" placeholder="https://example.com/image.jpg">
                @error('media_url') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Interest Tags <span style="color:var(--muted)">(determines who gets notified)</span></label>
                @if($interests->isEmpty())
                    <p style="color:var(--muted);font-size:.85rem">No interests defined yet. <a href="{{ route('admin.interests.create') }}" style="color:var(--accent2)">Add some</a> first.</p>
                @else
                    <div class="checkbox-grid">
                        @foreach($interests as $interest)
                            <label class="checkbox-item">
                                <input type="checkbox" name="tag_ids[]" value="{{ $interest->id }}"
                                    {{ in_array($interest->id, old('tag_ids', [])) ? 'checked' : '' }}>
                                <span>{{ $interest->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('tag_ids') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:12px;margin-bottom:20px;font-size:.82rem;color:var(--muted)">
                <i class="fa-solid fa-bell" style="color:#f59e0b;margin-right:6px"></i>
                Once published, the system will identify all users subscribed to the selected tags and send them a push notification via Firebase Cloud Messaging.
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Publish & Notify</button>
        </form>
    </div>
</div>
@endsection
