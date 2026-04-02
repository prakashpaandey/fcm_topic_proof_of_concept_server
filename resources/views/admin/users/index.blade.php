@extends('admin.layout')
@section('title', 'Users')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-users" style="color:#a78bfa;margin-right:10px"></i>Users</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create User</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Name</th>
                <th>Interests</th>
                <th>Devices</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td style="color:var(--muted)">{{ $user->id }}</td>
                <td><strong>{{ $user->username }}</strong></td>
                <td style="color:var(--muted)">{{ $user->name ?: '—' }}</td>
                <td><span class="badge badge-accent">{{ $user->interests_count }}</span></td>
                <td><span class="badge badge-muted">{{ $user->device_tokens_count }}</span></td>
                <td style="color:var(--muted);font-size:.8rem">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this user?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:32px">No users found. <a href="{{ route('admin.users.create') }}" style="color:var(--accent2)">Create one</a>.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px 16px">{{ $users->links() }}</div>
</div>
@endsection
