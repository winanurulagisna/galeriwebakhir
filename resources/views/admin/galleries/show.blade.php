@extends('admin.layouts.app')

@section('title', 'Detail Galeri - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Album Galeri')

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.galeri.index') }}" class="text-blue-600 hover:text-blue-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-lg font-bold text-gray-900">{{ $gallery->title }}</h1>
        <a href="{{ route('admin.galeri.index') }}" 
           class="ml-auto inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
            Kembali ke Daftar Album
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-4">
        @if($gallery->caption)
            <p class="text-sm text-gray-700 mb-3">{{ $gallery->caption }}</p>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            @forelse($gallery->photos as $photo)
                <div class="group relative">
                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $gallery->title }}" class="w-full h-32 object-cover rounded-lg group-hover:opacity-90 transition-opacity">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors rounded-lg flex items-center justify-center">
                        <a href="{{ asset('storage/' . $photo->file_path) }}" target="_blank" class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-search-plus text-white text-lg"></i>
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-500 col-span-full text-center py-8">Belum ada foto.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection


