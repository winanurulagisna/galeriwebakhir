@extends('admin.layouts.app')

@section('title', 'Tambah Profil Sekolah')
@section('page-title', 'Tambah Profil Sekolah')

@section('content')
<form action="{{ route('admin.profile.store') }}" method="POST">
    @csrf
    <div class="form-group mb-3">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control">
    </div>
    <div class="form-group mb-3">
        <label>Isi</label>
        <textarea name="isi" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
@endsection
