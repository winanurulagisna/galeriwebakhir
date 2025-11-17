@extends('public.layouts.app')

@section('title', 'Unduhan Saya')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Total Unduhan -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 rounded-full text-green-600">
                        <i class="fas fa-download text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Unduhan</p>
                        <p class="text-2xl font-semibold">{{ $downloads->total() }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Total Like -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-red-100 rounded-full text-red-600">
                        <i class="fas fa-heart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Like</p>
                        <p class="text-2xl font-semibold">{{ $likeCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Total Komentar -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <i class="fas fa-comment text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Komentar</p>
                        <p class="text-2xl font-semibold">{{ $commentCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    <i class="fas fa-download"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Unduhan Saya</h1>
                    <p class="text-gray-600">Riwayat foto yang pernah Anda unduh</p>
                </div>
            </div>
        </div>

        <!-- Downloads List -->
        @if($downloads->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($downloads as $download)
                    @if($download->photo)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        {{-- fix: tampilkan foto unduhan menggunakan relasi photo --}}
                        <img src="{{ asset($download->photo->optimal_url ?? $download->photo->file_path ?? 'https://via.placeholder.com/400x300') }}" 
                             alt="{{ $download->photo->caption ?? 'Foto' }}" 
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $download->photo->caption ?? ($download->photo->gallery->judul ?? 'Foto') }}</h3>
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                <span><i class="far fa-clock"></i> {{ $download->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('galeri.show', $download->photo->gallery_id ?? '#') }}" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                                <a href="{{ asset($download->photo->file_path) }}" download class="flex-1 text-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                    <i class="fas fa-download"></i> Unduh Lagi
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="mt-6">
                {{ $downloads->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-download text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Unduhan</h3>
                <p class="text-gray-600 mb-6">Anda belum mengunduh foto apapun</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-images"></i>
                    Jelajahi Galeri
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
