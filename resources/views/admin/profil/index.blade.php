@extends('admin.layouts.app')

@section('title', 'Kelola Profil Sekolah')
@section('page-title', 'Kelola Profil Sekolah')

@section('content')
<div class="space-y-4">
    <a href="{{ route('admin.profile.create') }}" class="btn btn-primary">Tambah Profil</a>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Isi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- Nanti looping data profile di sini --}}
            <tr>
                <td>Profil Sekolah</td>
                <td>Isi profil sekolah...</td>
                <td>
                    <a href="{{ route('admin.profile.show', 1) }}" class="btn btn-info btn-sm">Lihat</a>
                    <a href="{{ route('admin.profile.edit', 1) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.profile.destroy', 1) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
