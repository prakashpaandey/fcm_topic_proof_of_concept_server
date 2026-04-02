@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="color:#6c63ff"><i class="fa-solid fa-users"></i></div>
        <div class="stat-label">Total Users</div>
        <div class="stat-value" style="color:#a78bfa">{{ $stats['users'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#22d3ee"><i class="fa-solid fa-tags"></i></div>
        <div class="stat-label">Interests</div>
        <div class="stat-value" style="color:#22d3ee">{{ $stats['interests'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#f59e0b"><i class="fa-solid fa-newspaper"></i></div>
        <div class="stat-label">Total Posts</div>
        <div class="stat-value" style="color:#f59e0b">{{ $stats['posts'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#22c55e"><i class="fa-solid fa-paper-plane"></i></div>
        <div class="stat-label">Notifications Sent</div>
        <div class="stat-value" style="color:#22c55e">{{ $stats['notifications_sent'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:#ef4444"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-label">Failed Deliveries</div>
        <div class="stat-value" style="color:#ef4444">{{ $stats['notifications_failed'] }}</div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px">

    <div class="card">
        <div class="card-header">
            <span><i class="fa-solid fa-newspaper" style="color:#f59e0b;margin-right:8px"></i> Recent Posts</span>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">+ New Post</a>
        </div>
        <table>
            <thead><tr><th>Title</th><th>Tags</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($recentPosts as $post)
                <tr>
                    <td><a href="{{ route('admin.posts.show', $post) }}" style="color:var(--accent2);text-decoration:none">{{ Str::limit($post->title, 35) }}</a></td>
                    <td>
                        @foreach($post->tags as $tag)
                            <span class="badge badge-accent">{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td style="color:var(--muted);font-size:.8rem">{{ $post->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="color:var(--muted);text-align:center;padding:24px">No posts yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header">
            <span><i class="fa-solid fa-bell" style="color:#22c55e;margin-right:8px"></i> Recent Notification Logs</span>
        </div>
        <table>
            <thead><tr><th>User</th><th>Post</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($recentLogs as $log)
                <tr>
                    <td style="font-size:.82rem">{{ $log->user->username ?? '—' }}</td>
                    <td style="font-size:.82rem;color:var(--muted)">{{ Str::limit($log->post->title ?? '—', 25) }}</td>
                    <td>
                        @if($log->status === 'success')
                            <span class="badge badge-success">Sent</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" style="color:var(--muted);text-align:center;padding:24px">No logs yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
