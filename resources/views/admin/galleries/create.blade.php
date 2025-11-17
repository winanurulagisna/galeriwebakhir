@extends('layouts.app')

@section('title', 'Tambah Galeri')

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
            <h1 class="text-3xl font-bold text-gray-900">Tambah Galeri Baru</h1>
        </div>
        <p class="mt-2 text-sm text-gray-700">Upload foto baru ke galeri</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" name="title" id="title" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm @error('title') border-red-500 @enderror"
                       placeholder="Masukkan judul galeri"
                       value="{{ old('title') }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="caption" class="block text-sm font-medium text-gray-700">Caption</label>
                <textarea name="caption" id="caption" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm @error('caption') border-red-500 @enderror"
                          placeholder="Masukkan deskripsi foto (opsional)">{{ old('caption') }}</textarea>
                @error('caption')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="photos" class="block text-sm font-medium text-gray-700">File Foto (bisa lebih dari satu)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md @error('photos.*') border-red-500 @enderror">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="photos" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-secondary focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                <span>Upload foto</span>
                                <input id="photos" name="photos[]" type="file" class="sr-only" accept="image/*" multiple required>
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP hingga 4MB per foto</p>
                    </div>
                </div>
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
                            <option value="{{ $post['id'] }}" {{ old('post_id') == $post['id'] ? 'selected' : '' }}>
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
                    Simpan Galeri
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview multiple images
    document.getElementById('photos').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const container = document.querySelector('.space-y-1');
        // Remove old previews
        container.querySelectorAll('img.preview-thumb').forEach(el => el.remove());
        files.slice(0, 6).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.createElement('img');
                preview.src = ev.target.result;
                preview.className = 'preview-thumb inline-block h-24 w-24 object-cover rounded-lg mt-2 mr-2';
                container.appendChild(preview);
            }
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush
@endsection
