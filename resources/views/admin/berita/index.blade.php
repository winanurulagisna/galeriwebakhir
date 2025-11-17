@extends('admin.layouts.app')

@section('title', 'Berita - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Kelola Berita Terkini')

@section('content')
<div class="space-y-6">

    <!-- Statistik Galeri -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Kelola Galeri</h2>
            <p class="text-gray-600">Kelola semua foto dan dokumentasi kegiatan sekolah</p>
        </div>
    </div>

    <!-- Statistik Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Foto -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 flex items-center">
            <div class="flex-shrink-0 w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-image text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Foto</p>
                <p class="text-2xl font-bold text-gray-900">6</p>
            </div>
        </div>

        <!-- Foto Terbaru -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Foto Terbaru</p>
                <p class="text-2xl font-bold text-gray-900">4</p>
            </div>
        </div>

        <!-- Total Views -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 flex items-center">
            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-eye text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Views</p>
                <p class="text-2xl font-bold text-gray-900">809</p>
            </div>
        </div>
    </div>

    <!-- Header Actions (Berita) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Daftar Berita</h2>
            <p class="text-gray-600">Kelola semua berita sekolah</p>
        </div>
        <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Berita
        </button>
    </div>

    @php
        $berita = [
            [
                'title' => 'Upacara Peringatan Hari Kemerdekaan Indonesia',
                'date' => '17 Agustus 2024',
                'category' => 'Kegiatan Sekolah',
                'image' => '/images/upacara17.JPG',
                'content' => 'Kegiatan upacara bendera dalam rangka memperingati Hari Kemerdekaan Republik Indonesia...',
                'icon' => 'fas fa-flag',
                'url' => '/berita'
            ],
            [
                'title' => 'Kegiatan TRANSFORKR4B',
                'date' => '30 Oktober 2024',
                'category' => 'Teknologi',
                'image' => '/images/transforkrab.JPG',
                'content' => 'Program TRANSFOKR4B untuk meningkatkan kompetensi siswa dalam bidang TIK...',
                'icon' => 'fas fa-laptop',
                'url' => '/berita'
            ],
            [
                'title' => 'Masa Pengenalan Lingkungan Sekolah',
                'date' => '19 Juli 2024',
                'category' => 'Akademik',
                'image' => '/images/mpls.JPG',
                'content' => 'MPLS tahun ajaran 2024/2025 berlangsung dengan lancar dan penuh semangat...',
                'icon' => 'fas fa-graduation-cap',
                'url' => '/berita'
            ],
            [
                'title' => 'Peringatan Maulid Nabi Muhammad SAW',
                'date' => '15 September 2024',
                'category' => 'Keagamaan',
                'image' => '/images/maulidnabi.JPG',
                'content' => 'Kegiatan peringatan Maulid Nabi dengan berbagai kegiatan keagamaan...',
                'icon' => 'fas fa-mosque',
                'url' => '/berita'
            ],
            [
                'title' => 'Festival Adat (FESDA)',
                'date' => '17 Desember 2024',
                'category' => 'Budaya',
                'image' => '/images/fedat.JPG',
                'content' => 'Festival budaya menampilkan kesenian dan adat istiadat Indonesia...',
                'icon' => 'fas fa-theater-masks',
                'url' => '/berita'
            ],
            [
                'title' => 'Kunjungan Industri',
                'date' => '25 September 2024',
                'category' => 'Industri',
                'image' => '/images/kunjungan.JPG',
                'content' => 'Siswa melakukan kunjungan industri untuk mengenal dunia kerja...',
                'icon' => 'fas fa-industry',
                'url' => '/berita'
            ],
        ];
    @endphp

    <!-- Berita Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($berita as $item)
        <div class="bg-white rounded-xl shadow hover:shadow-lg overflow-hidden flex flex-col transition-transform hover:-translate-y-1">
            
            <!-- Gambar -->
            <img src="{{ asset($item['image']) }}" 
                 alt="{{ $item['title'] }}" 
                 class="h-48 w-full object-cover transition-transform duration-300 hover:scale-105">
            
            <!-- Konten -->
            <div class="p-4 flex flex-col flex-1">
                <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full w-fit mb-2">
                    <i class="{{ $item['icon'] }} mr-1"></i> {{ $item['category'] }}
                </span>
                <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ $item['title'] }}</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-3">{{ $item['content'] }}</p>
                <div class="text-sm text-gray-500 mt-auto">
                    <i class="fas fa-calendar mr-1"></i>{{ $item['date'] }}
                </div>

                <!-- Aksi -->
                <div class="flex items-center justify-between mt-4">
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </button>
                        <a href="{{ $item['url'] }}" class="px-3 py-1 bg-green-100 text-green-600 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            Lihat
                        </a>
                    </div>
                    <button class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors">
                        <i class="fas fa-trash mr-1"></i>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if(count($berita) === 0)
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <div class="text-gray-500">
            <i class="fas fa-newspaper text-6xl mb-4"></i>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Belum ada berita</h3>
            <p class="text-gray-600 mb-6">Mulai dengan menambahkan berita pertama</p>
            <button class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Berita Pertama
            </button>
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
