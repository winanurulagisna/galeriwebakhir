@extends('layouts.app')

@section('title', 'Edit Galeri')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.galeri.index') }}" class="text-primary hover:text-secondary mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Galeri</h1>
        </div>
        <p class="mt-2 text-sm text-gray-700">Edit informasi galeri</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.galeri.update', $gallery->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" id="title" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm @error('title') border-red-500 @enderror"
                       placeholder="Masukkan judul galeri"
                       value="{{ old('title', $gallery['title'] ?? '') }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="caption" class="block text-sm font-medium text-gray-700">Caption</label>
                <textarea name="caption" id="caption" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm @error('caption') border-red-500 @enderror"
                          placeholder="Masukkan deskripsi foto (opsional)">{{ old('caption', $gallery['caption'] ?? '') }}</textarea>
                @error('caption')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Foto Saat Ini</label>
                <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                    @forelse($gallery->photos as $photo)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $gallery->title }}" class="h-28 w-full object-cover rounded-lg">
                            <label class="absolute top-1 right-1 bg-white bg-opacity-80 rounded px-1 text-xs">
                                <input type="checkbox" name="remove_photo_ids[]" value="{{ $photo->id }}"> Hapus
                            </label>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada foto di galeri ini.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label for="photos" class="block text-sm font-medium text-gray-700">Tambah Foto Baru (opsional)</label>
                <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('photos.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if(isset($posts) && count($posts) > 0)
                <div>
                    <label for="post_id" class="block text-sm font-medium text-gray-700">Hubungkan dengan Post (Opsional)</label>
                    <select name="post_id" id="post_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="">Pilih post (opsional)</option>
                        @foreach($posts as $post)
                            <option value="{{ $post['id'] }}" 
                                    {{ old('post_id', $gallery['post_id'] ?? '') == $post['id'] ? 'selected' : '' }}>
                                {{ $post['title'] ?? 'Untitled' }}
                            </option>
                        @endforeach
                    </select>
                    @error('post_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.galeri.index') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Galeri
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
