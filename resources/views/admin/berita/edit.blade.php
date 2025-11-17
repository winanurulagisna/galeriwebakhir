@extends('admin.layouts.app')

@section('title', 'Edit Informasi - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Edit Informasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Edit Informasi</h2>
            <p class="text-gray-600">Edit informasi yang sudah ada</p>
        </div>

        <form action="{{ route('admin.berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Berita <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $berita->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                       placeholder="Masukkan judul berita"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Isi Berita <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="8"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror"
                          placeholder="Tulis isi berita lengkap di sini..."
                          required>{{ old('content', $berita->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Image -->
            @if($berita->image)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                <div class="flex items-center space-x-4">
                    <img src="{{ asset($berita->image) }}" 
                         alt="Current Image" 
                         class="w-32 h-24 object-cover rounded-lg border border-gray-300">
                    <div class="text-sm text-gray-500">
                        <p>Gambar saat ini akan tetap digunakan jika tidak memilih gambar baru</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- New Image -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    Ganti Gambar (Opsional)
                </label>
                <div class="flex items-center space-x-4">
                    <input type="file" 
                           name="image" 
                           id="image" 
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('image') border-red-500 @enderror">
                </div>
                <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar.</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview New Image -->
            <div id="imagePreview" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview Gambar Baru</label>
                <div class="w-48 h-32 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden">
                    <img id="preview" src="" alt="Preview" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.berita.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Berita
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const preview = document.getElementById('preview');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('hidden');
        }
    });
});
</script>
@endsection
