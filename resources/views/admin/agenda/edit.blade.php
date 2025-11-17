@extends('admin.layouts.app')

@section('title', 'Edit Agenda - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Edit Agenda')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Edit Agenda</h2>
            <p class="text-gray-600">Edit informasi agenda yang sudah ada</p>
        </div>

        <form action="{{ route('admin.agenda.update', $agenda->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Agenda <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $agenda->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('title') border-red-500 @enderror"
                       placeholder="Masukkan judul agenda"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>



            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Agenda <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="8"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('content') border-red-500 @enderror"
                          placeholder="Tulis deskripsi lengkap agenda sekolah..."
                          required>{{ old('content', $agenda->body) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.agenda') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Agenda
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
