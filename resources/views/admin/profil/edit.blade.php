@extends('admin.layouts.app')

@section('title', 'Edit Profil Sekolah')
@section('page-title', 'Edit Profil Sekolah')

@section('content')
<form action="{{ route('admin.profile.update', $profile->id ?? 1) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group mb-3">
        <label>Judul</label>
        <input type="text" name="judul" value="{{ $profile->judul ?? '' }}" class="form-control">
    </div>
    <div class="form-group mb-3">
        <label>Isi</label>
        <textarea name="isi" class="form-control">{{ $profile->isi ?? '' }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endsection
