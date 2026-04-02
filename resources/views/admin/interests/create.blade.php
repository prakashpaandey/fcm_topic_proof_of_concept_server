@extends('admin.layout')
@section('title', 'Add Interest')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-tag" style="color:#22d3ee;margin-right:10px"></i>Add Interest</h1>
    <a href="{{ route('admin.interests.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:480px">
    <div class="card-header">New Interest Category</div>
    <div style="padding:24px">
        <form action="{{ route('admin.interests.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Interest Name <span style="color:#ef4444">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Cricket, Music, Travel..." autofocus required>
                @error('name') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div style="background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.2);border-radius:8px;padding:12px;margin-bottom:20px;font-size:.82rem;color:var(--muted)">
                <i class="fa-solid fa-circle-info" style="color:#22d3ee;margin-right:6px"></i>
                Interests are visible to mobile app users who can subscribe to them. Posts tagged with an interest will notify subscribed users.
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Interest</button>
        </form>
    </div>
</div>
@endsection
