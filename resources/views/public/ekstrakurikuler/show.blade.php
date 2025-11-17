@extends('public.layouts.app')

@section('title', $ekstrakurikuler->title . ' - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('ekstrakurikuler.index') }}" class="inline-flex items-center text-[#023859] font-semibold hover:underline">
            <span class="mr-2">&larr;</span> Kembali ke Daftar Ekstrakurikuler
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <article class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Featured Image -->
                @if($ekstrakurikuler->photos->first())
                @php
                    $file = $ekstrakurikuler->photos->first()->file ?? null;
                    $mainImage = $file ? \App\Helpers\ImageUrlHelper::getSafeImageUrl($file) : asset('images/default-ekstrakurikuler.jpg');
                @endphp
                <div class="aspect-video bg-gray-200">
                    <img src="{{ $mainImage }}" 
                         alt="{{ $ekstrakurikuler->title }}" 
                         class="w-full h-full object-cover">
                </div>
                @else
                <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-star text-blue-600 text-6xl mb-4"></i>
                        <h1 class="text-3xl font-bold text-blue-800">{{ $ekstrakurikuler->title }}</h1>
                    </div>
                </div>
                @endif
                
                <div class="p-8">
                    <!-- Meta Information -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 bg-green-600 text-white rounded-full text-sm">
                                Ekstrakurikuler
                            </span>
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">
                                Aktif
                            </span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-calendar mr-1"></i>
                            <span>{{ $ekstrakurikuler->created_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl font-bold text-gray-800 mb-6 leading-tight">
                        {{ $ekstrakurikuler->title }}
                    </h1>

                    <!-- Description -->
                    @if($ekstrakurikuler->description)
                    <div class="prose prose-lg max-w-none text-gray-700 mb-8">
                        {!! $ekstrakurikuler->description !!}
                    </div>
                    @endif

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar text-white"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800">Tanggal Dibuat</h3>
                            </div>
                            <p class="text-gray-600">{{ $ekstrakurikuler->created_at->format('d M Y') }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="flex items-center mb-3">
                                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800">Status</h3>
                            </div>
                            <p class="text-gray-600">Aktif</p>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pendaftaran</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">Untuk informasi lebih lanjut atau pendaftaran, silakan hubungi:</p>
                                <p class="font-semibold text-gray-800">Pembina Ekstrakurikuler</p>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('kontak.index') }}" 
                                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Hubungi Kami
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Other Extracurriculars -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Ekstrakurikuler Lainnya</h2>
                <div class="space-y-4">
                    @if(isset($relatedEkstrakurikuler) && $relatedEkstrakurikuler->count() > 0)
                        @foreach($relatedEkstrakurikuler as $other)
                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                    @if($other->photos->first())
                                        @php
                                            $ofile = $other->photos->first()->file ?? null;
                                            $thumb = $ofile ? \App\Helpers\ImageUrlHelper::getSafeImageUrl($ofile) : asset('images/default-ekstrakurikuler.jpg');
                                        @endphp
                                        <img src="{{ $thumb }}" 
                                             alt="{{ $other->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-star text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2">
                                    <a href="{{ route('ekstrakurikuler.show', $other) }}" 
                                       class="hover:text-blue-600 transition-colors">
                                        {{ $other->title }}
                                    </a>
                                </h3>
                                <div class="flex items-center space-x-2 text-sm text-gray-500">
                                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs">
                                        Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">Tidak ada ekstrakurikuler lainnya.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-blue-700">Total Anggota</span>
                        <span class="font-bold text-blue-800">{{ $ekstrakurikuler->members }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-blue-700">Status</span>
                        <span class="font-bold text-blue-800">{{ ucfirst($ekstrakurikuler->status) }}</span>
                    </div>
                    @if($ekstrakurikuler->schedule)
                    <div class="flex items-center justify-between">
                        <span class="text-blue-700">Jadwal</span>
                        <span class="font-bold text-blue-800 text-sm">{{ $ekstrakurikuler->schedule }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Back to Extracurriculars -->
            <div class="bg-green-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-green-800 mb-3">Jelajahi Ekstrakurikuler Lainnya</h3>
                <p class="text-green-700 mb-4">Temukan berbagai kegiatan ekstrakurikuler yang tersedia di SMKN 4 KOTA BOGOR</p>
                <a href="{{ route('ekstrakurikuler.index') }}" 
                   class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Lihat Semua Ekstrakurikuler
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.prose {
    line-height: 1.7;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #1f2937;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.prose p {
    margin-bottom: 1rem;
}

.prose ul, .prose ol {
    margin-bottom: 1rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection