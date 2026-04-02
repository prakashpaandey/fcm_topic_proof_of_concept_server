@extends('admin.layout')
@section('title', 'Create User')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-user-plus" style="color:#a78bfa;margin-right:10px"></i>Create User</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:600px">
    <div class="card-header">New Mobile App User</div>
    <div style="padding:24px">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="username">Username <span style="color:#ef4444">*</span></label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="e.g. john_doe" required>
                @error('username') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. John Doe">
                @error('name') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="profile_details">Profile Details</label>
                <textarea id="profile_details" name="profile_details" placeholder="Any extra info about this user...">{{ old('profile_details') }}</textarea>
                @error('profile_details') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password <span style="color:#ef4444">*</span></label>
                <input id="password" type="password" name="password" placeholder="Min. 6 characters" required>
                @error('password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span style="color:#ef4444">*</span></label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat password" required>
            </div>

            <div style="background:rgba(108,99,255,.08);border:1px solid rgba(108,99,255,.2);border-radius:8px;padding:12px;margin-bottom:20px;font-size:.82rem;color:var(--muted)">
                <i class="fa-solid fa-circle-info" style="color:#6c63ff;margin-right:6px"></i>
                After creating this user, provide them the <strong style="color:var(--text)">username</strong> and <strong style="color:var(--text)">password</strong> so they can log into the mobile application.
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Create User</button>
        </form>
    </div>
</div>
@endsection
