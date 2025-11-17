@extends('admin.layouts.app')

@section('title', 'Kelola Galeri - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Kelola Galeri')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Kelola Galeri</h2>
            <p class="text-xs text-gray-600">Kelola semua foto dan dokumentasi kegiatan sekolah</p>
        </div>
        <a href="{{ route('admin.galeri.create') }}" 
           class="inline-flex items-center px-3 py-1.5 text-sm bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-1.5 text-xs"></i>
            Upload Foto
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total Foto</p>
                    <p class="text-xl font-bold text-gray-900">{{ $totalGaleri }}</p>
                </div>
                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-images text-green-600 text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Foto Terbaru</p>
                    <p class="text-xl font-bold text-gray-900">{{ $recentGaleri }}</p>
                </div>
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total Views</p>
                    <p class="text-xl font-bold text-gray-900">{{ $totalViews }}</p>
                </div>
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-purple-600 text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Galeri Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($galeri as $item)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow group">
            <!-- Image -->
            <div class="relative h-36 bg-cover bg-center" 
                 style="background-image: url('{{ asset($item->image) }}')">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute top-2 right-2">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800">
                        <i class="fas fa-image mr-0.5 text-[8px]"></i>
                        Galeri
                    </span>
                </div>
                <div class="absolute bottom-2 left-2 right-2">
                    <h3 class="text-white text-sm font-bold mb-0.5 line-clamp-2">{{ $item->title }}</h3>
                    <div class="flex items-center space-x-2 text-white/80 text-[10px]">
                        <span>
                            <i class="fas fa-eye mr-0.5"></i>
                            {{ rand(50, 300) }}
                        </span>
                        <span>
                            <i class="fas fa-calendar mr-0.5"></i>
                            {{ $item->created_at ? $item->created_at->format('d M Y') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-3">
                <p class="text-xs text-gray-600 mb-3 leading-relaxed line-clamp-2">
                    {{ $item->description }}
                </p>
                
                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <div class="flex space-x-1">
                        <a href="{{ asset($item->image) }}" 
                           target="_blank"
                           class="px-2 py-1 bg-blue-100 text-blue-600 rounded text-[11px] font-medium hover:bg-blue-200 transition-colors">
                            <i class="fas fa-eye mr-0.5 text-[10px]"></i>
                            Lihat
                        </a>
                        <a href="#" 
                           class="px-2 py-1 bg-green-100 text-green-600 rounded text-[11px] font-medium hover:bg-green-200 transition-colors">
                            <i class="fas fa-edit mr-0.5 text-[10px]"></i>
                            Edit
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admin.galeri.destroy', $item->id) }}" 
                          class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-2 py-1 bg-red-100 text-red-600 rounded text-[11px] font-medium hover:bg-red-200 transition-colors">
                            <i class="fas fa-trash mr-0.5 text-[10px]"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="text-gray-500">
                <i class="fas fa-images text-4xl mb-3"></i>
                <h3 class="text-base font-medium text-gray-900 mb-2">Belum ada foto di galeri</h3>
                <p class="text-xs text-gray-600 mb-4">Mulai dengan mengupload foto pertama ke galeri sekolah</p>
                <a href="{{ route('admin.galeri.create') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-upload mr-1.5 text-xs"></i>
                    Upload Foto Pertama
                </a>
            </div>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($galeri->hasPages())
    <div class="bg-white rounded-lg shadow-sm p-3">
        {{ $galeri->links() }}
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Card hover effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Action button hover effects */
.transition-colors {
    transition: all 0.2s ease-in-out;
}

.hover\:bg-blue-200:hover {
    background-color: #dbeafe;
}

.hover\:bg-green-200:hover {
    background-color: #bbf7d0;
}

.hover\:bg-red-200:hover {
    background-color: #fecaca;
}
</style>
@endsection
