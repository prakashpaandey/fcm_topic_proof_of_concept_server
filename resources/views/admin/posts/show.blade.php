@extends('admin.layout')
@section('title', 'Post Details')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-file-invoice" style="color:#f59e0b;margin-right:10px"></i>Post Details</h1>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Posts</a>
</div>

<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:20px">
    
    <div class="card">
        <div class="card-header">Content Preview</div>
        <div style="padding:24px">
            <h2 style="font-size:1.5rem; margin-bottom:12px">{{ $post->title }}</h2>
            <div style="color:var(--muted); font-size:0.85rem; margin-bottom:20px; display:flex; gap:15px">
                <span><i class="fa-solid fa-user"></i> {{ $post->author->username ?? 'Unknown' }}</span>
                <span><i class="fa-solid fa-calendar"></i> {{ $post->created_at->format('d M Y, H:i') }}</span>
            </div>

            @if($post->media_url)
                <div style="margin-bottom:20px; border-radius:8px; overflow:hidden; border:1px solid var(--border)">
                    @if(Str::endsWith($post->media_url, ['.mp4', '.webm']))
                        <video src="{{ $post->media_url }}" controls style="width:100%; display:block"></video>
                    @else
                        <img src="{{ $post->media_url }}" alt="Post Media" style="width:100%; display:block">
                    @endif
                </div>
            @endif

            <div style="line-height:1.6; white-space:pre-wrap">{{ $post->text_content }}</div>

            <div style="margin-top:30px; padding-top:15px; border-top:1px solid var(--border)">
                <label style="margin-bottom:8px">Targeted Interests:</label>
                <div>
                    @forelse($post->tags as $tag)
                        <span class="badge badge-accent" style="font-size:0.8rem; padding:5px 12px">{{ $tag->name }}</span>
                    @empty
                        <span style="color:var(--muted)">No tags associated.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">Notification Summary</div>
            <div style="padding:24px">
                <div style="display:flex; justify-content:space-between; margin-bottom:12px">
                    <span style="color:var(--muted)">Successful Sent:</span>
                    <span class="badge badge-success">{{ $post->notificationLogs->where('status', 'success')->count() }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:12px">
                    <span style="color:var(--muted)">Failed Attempts:</span>
                    <span class="badge badge-danger">{{ $post->notificationLogs->where('status', 'failed')->count() }}</span>
                </div>
                <div style="display:flex; justify-content:space-between">
                    <span style="color:var(--muted)">Total Targeted:</span>
                    <span class="badge badge-muted">{{ $post->notificationLogs->count() }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Delivery Details</div>
            <div style="max-height:500px; overflow-y:auto">
                <table>
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($post->notificationLogs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight:500">{{ $log->user->username ?? 'Unknown' }}</div>
                                    <div style="font-size:0.7rem; color:var(--muted); word-break:break-all">{{ Str::limit($log->fcm_token, 20) }}</div>
                                </td>
                                <td>
                                    @if($log->status === 'success')
                                        <span class="badge badge-success">Delivered</span>
                                    @else
                                        <div class="badge badge-danger">Failed</div>
                                        @if($log->error_message)
                                            <div style="font-size:0.65rem; color:var(--danger); margin-top:4px">{{ $log->error_message }}</div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align:center; color:var(--muted); padding:20px">No notification history.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
