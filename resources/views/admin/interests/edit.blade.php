@extends('admin.layout')
@section('title', 'Edit Interest')

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-pen-to-square" style="color:#22d3ee;margin-right:10px"></i>Edit Interest</h1>
    <a href="{{ route('admin.interests.index') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="card" style="max-width:480px">
    <div class="card-header">Editing: <strong>{{ $interest->name }}</strong></div>
    <div style="padding:24px">
        <form action="{{ route('admin.interests.update', $interest) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label for="name">Interest Name <span style="color:#ef4444">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $interest->name) }}" required>
                @error('name') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Update Interest</button>
        </form>
    </div>
</div>
@endsection
