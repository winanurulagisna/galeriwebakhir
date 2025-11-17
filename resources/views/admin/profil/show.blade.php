@extends('admin.layouts.app')

@section('title', 'Detail Profil Sekolah')
@section('page-title', 'Detail Profil Sekolah')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>{{ $profile->judul ?? 'Judul Profil' }}</h3>
        <p>{{ $profile->isi ?? 'Isi profil sekolah' }}</p>
        <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
