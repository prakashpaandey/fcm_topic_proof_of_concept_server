@extends('admin.layout')
@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-user-pen" style="color:#a78bfa;margin-right:10px"></i>Edit User</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:600px">
    <div class="card-header">Editing: <strong>{{ $user->username }}</strong></div>
    <div style="padding:24px">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="username">Username <span style="color:#ef4444">*</span></label>
                <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required>
                @error('username') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="e.g. John Doe">
                @error('name') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="profile_details">Profile Details</label>
                <textarea id="profile_details" name="profile_details">{{ old('profile_details', $user->profile_details) }}</textarea>
                @error('profile_details') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">New Password <span style="color:var(--muted)">(leave blank to keep current)</span></label>
                <input id="password" type="password" name="password" placeholder="Min. 6 characters">
                @error('password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat password">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update User</button>
        </form>
    </div>
</div>
@endsection
