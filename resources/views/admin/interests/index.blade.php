@extends('admin.layout')
@section('title', 'Interests')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-tags" style="color:#22d3ee;margin-right:10px"></i>Interests</h1>
    <a href="{{ route('admin.interests.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Interest</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Subscribers</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($interests as $interest)
            <tr>
                <td style="color:var(--muted)">{{ $interest->id }}</td>
                <td><strong>{{ $interest->name }}</strong></td>
                <td><span class="badge badge-accent">{{ $interest->users_count }} users</span></td>
                <td style="color:var(--muted);font-size:.8rem">{{ $interest->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.interests.edit', $interest) }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                    <form action="{{ route('admin.interests.destroy', $interest) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this interest?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:32px">No interests found. <a href="{{ route('admin.interests.create') }}" style="color:var(--accent2)">Add one</a>.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="padding:12px 16px">{{ $interests->links() }}</div>
</div>
@endsection
