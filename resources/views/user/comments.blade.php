@extends('public.layouts.app')

@section('title', 'Komentar Saya')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Total Komentar -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <i class="fas fa-comment text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Komentar</p>
                        <p class="text-2xl font-semibold">{{ $comments->total() }}</p>
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
            
            <!-- Total Unduhan -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 rounded-full text-green-600">
                        <i class="fas fa-download text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Unduhan</p>
                        <p class="text-2xl font-semibold">{{ $downloadCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Komentar Saya</h1>
                    <p class="text-gray-600">Lihat semua komentar yang pernah Anda buat</p>
                </div>
            </div>
        </div>

        <!-- Comments List -->
        @if($comments->count() > 0)
            <div class="space-y-4">
                @foreach($comments as $comment)
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-start gap-4">
                            <img src="{{ $comment->photo_url ?? 'https://via.placeholder.com/100' }}" 
                                 alt="Photo" 
                                 class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-2">{{ $comment->photo_title ?? 'Foto' }}</h3>
                                <p class="text-gray-600 mb-3">{{ $comment->content }}</p>
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span><i class="far fa-clock"></i> {{ $comment->created_at->diffForHumans() }}</span>
                                    <a href="#" class="text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-eye"></i> Lihat Foto
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Komentar</h3>
                <p class="text-gray-600 mb-6">Anda belum membuat komentar apapun</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-images"></i>
                    Jelajahi Galeri
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
