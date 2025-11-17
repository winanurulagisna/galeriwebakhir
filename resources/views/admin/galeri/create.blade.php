@extends('admin.layouts.app')

@section('title', 'Upload Foto - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Upload Foto ke Galeri')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Upload Foto Baru</h2>
            <p class="text-gray-600">Tambahkan foto baru ke galeri sekolah</p>
        </div>

        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Foto <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('title') border-red-500 @enderror"
                       placeholder="Masukkan judul foto"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Foto <span class="text-red-500">*</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('description') border-red-500 @enderror"
                          placeholder="Jelaskan tentang foto ini..."
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Foto <span class="text-red-500">*</span>
                </label>
                
                <!-- Drag & Drop Area -->
                <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-green-400 transition-colors">
                    <div class="space-y-4">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Upload Foto</p>
                            <p class="text-gray-500">Drag & drop foto di sini atau klik untuk memilih</p>
                        </div>
                        <input type="file" 
                               name="image" 
                               id="image" 
                               accept="image/*"
                               class="hidden"
                               required>
                        <button type="button" 
                                onclick="document.getElementById('image').click()"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                            Pilih Foto
                        </button>
                    </div>
                </div>
                
                <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Image -->
            <div id="imagePreview" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview Foto</label>
                <div class="max-w-md">
                    <img id="preview" src="" alt="Preview" class="w-full h-auto rounded-lg border border-gray-300">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.galeri') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Foto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const preview = document.getElementById('preview');

    // File input change
    imageInput.addEventListener('change', function(e) {
        handleFile(e.target.files[0]);
    });

    // Drag and drop functionality
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-green-400', 'bg-green-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-green-400', 'bg-green-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-green-400', 'bg-green-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
            imageInput.files = files;
        }
    });

    // Click to select file
    dropZone.addEventListener('click', function() {
        imageInput.click();
    });

    function handleFile(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endsection
