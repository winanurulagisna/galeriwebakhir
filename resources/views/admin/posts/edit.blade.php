@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.posts') }}" class="text-primary hover:text-secondary mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Post</h1>
        </div>
        <p class="mt-2 text-sm text-gray-700">Edit artikel dan konten</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.posts.update', $post['id']) }}" method="POST" class="space-y-6 p-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Judul Post</label>
                <input type="text" name="title" id="title" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm @error('title') border-red-500 @enderror"
                       placeholder="Masukkan judul post"
                       value="{{ old('title', $post['title'] ?? '') }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if(isset($categories) && count($categories) > 0)
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category_id" id="category_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm @error('category_id') border-red-500 @enderror">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" 
                                    {{ old('category_id', $post['category_id'] ?? '') == $category['id'] ? 'selected' : '' }}>
                                {{ $category['judul'] ?? 'Untitled' }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Konten</label>
                <textarea name="content" id="content" rows="10" required
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm @error('content') border-red-500 @enderror"
                          placeholder="Tulis konten post Anda di sini...">{{ old('content', $post['content'] ?? '') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Anda dapat menggunakan HTML untuk formatting.</p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.posts') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
