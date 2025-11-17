@extends('public.layouts.app')

@section('title', 'Berita Sekolah - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    

    <div class="text-center mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Berita Sekolah</h1>
        <p class="text-xs md:text-sm text-gray-600 max-w-2xl mx-auto">Informasi terbaru dan berita penting dari SMKN 4 KOTA BOGOR</p>
    </div>

    @if(isset($posts) && $posts->count() > 0)
        <!-- Responsive card grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            @foreach($posts as $post)
            <div class="bg-white border border-gray-100 shadow-sm hover:shadow-md rounded-xl overflow-hidden transition h-full flex flex-col">
                <a href="{{ route('berita.show', $post) }}" class="block group">
                    <div class="aspect-[16/9] bg-gray-200 overflow-hidden">
                        @php $img = $post->image ? 'http://localhost:8000'.$post->image : 'http://localhost:8000/images/default-berita.jpg'; @endphp
                        <img src="{{ $img }}" alt="{{ $post->judul }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                </a>
                <div class="p-3 md:p-4 flex-1 flex flex-col">
                    @php
                        // Ambil URL sumber dari kolom khusus; fallback ke parsing isi jika kosong
                        $externalUrl = $post->url ?? null;
                        if (empty($externalUrl) && !empty($post->isi)) {
                            if (preg_match('/https?:\/\/[^\s"\']+/i', $post->isi, $m)) {
                                $externalUrl = $m[0];
                            }
                        }
                    @endphp
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h3 class="text-[#023859] font-semibold line-clamp-2 flex-1">{{ $post->judul }}</h3>
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-3 mb-3">{!! strip_tags($post->isi) !!}</p>
                    
                    <!-- Meta info -->
                    <div class="mt-auto pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-calendar text-gray-400"></i>
                            <span>{{ optional($post->created_at)->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
        {{ $posts->links() }}
    </div>
    @else
        <!-- No News -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <i class="fas fa-newspaper text-gray-400 text-6xl mb-6"></i>
                <p class="text-gray-600 mb-8">Berita akan ditampilkan di sini setelah ditambahkan melalui admin panel.</p>
                
                <!-- Sample News Categories -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Kategori Berita</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-graduation-cap text-blue-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Pendidikan</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-trophy text-yellow-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Prestasi</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-calendar text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Kegiatan</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center">
                            <i class="fas fa-bullhorn text-red-600 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-600">Pengumuman</p>
                        </div>
                    </div>
                </div>
            </div>
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
</style>
@endsection
