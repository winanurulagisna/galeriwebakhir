@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome / Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-base md:text-lg font-bold text-[#023859]">Selamat Datang di Dashboard</h2>
                <p class="text-xs text-gray-600">Ringkasan cepat kondisi situs dan tindakan yang bisa Anda lakukan.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.berita.index') }}" class="px-3 py-1.5 text-sm rounded-lg bg-[#023859] text-white hover:bg-[#01304b] font-medium">Kelola Berita</a>
                <a href="{{ route('admin.agenda.index') }}" class="px-3 py-1.5 text-sm rounded-lg bg-white border border-gray-300 text-[#023859] hover:bg-gray-50 font-medium">Kelola Agenda</a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Pengunjung Hari Ini</p>
                    <div class="mt-1 flex items-end gap-1.5">
                        <span class="text-lg md:text-xl font-bold text-[#023859]">123</span>
                        <span class="text-[10px] text-green-600 bg-green-50 px-1.5 py-0.5 rounded-full">+5%</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-users text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Admin Aktif</p>
                    <div class="mt-1 flex items-end gap-1.5">
                        <span class="text-lg md:text-xl font-bold text-[#023859]">3</span>
                        <span class="text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full">OK</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-user-shield text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Komentar Baru</p>
                    <div class="mt-1 flex items-end gap-1.5">
                        <span class="text-lg md:text-xl font-bold text-[#023859]">7</span>
                        <span class="text-[10px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-full">24h</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-comments text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-semibold">Status Server</p>
                    <div class="mt-1 flex items-end gap-1.5">
                        <span class="text-base md:text-lg font-bold text-[#023859]">Online</span>
                        <span class="text-[10px] text-green-600 bg-green-50 px-1.5 py-0.5 rounded-full">Normal</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fas fa-server text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row: Quick Actions + Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Quick Actions -->
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-3">
                <h3 class="text-sm font-bold text-[#023859] mb-2">Tindakan Cepat</h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.posts.create') }}" class="flex items-center gap-1.5 p-2 rounded-lg border border-gray-200 hover:border-[#023859] hover:bg-gray-50">
                        <i class="fas fa-plus text-[#023859] text-xs"></i>
                        <span class="text-xs font-medium">Tambah Berita</span>
                    </a>
                    <a href="{{ route('admin.agenda.index') }}" class="flex items-center gap-1.5 p-2 rounded-lg border border-gray-200 hover:border-[#023859] hover:bg-gray-50">
                        <i class="fas fa-calendar-plus text-[#023859] text-xs"></i>
                        <span class="text-xs font-medium">Tambah Agenda</span>
                    </a>
                    <a href="{{ route('admin.galeri.index') }}" class="flex items-center gap-1.5 p-2 rounded-lg border border-gray-200 hover:border-[#023859] hover:bg-gray-50">
                        <i class="fas fa-images text-[#023859] text-xs"></i>
                        <span class="text-xs font-medium">Kelola Galeri</span>
                    </a>
                    <a href="{{ route('admin.kategori.index') ?? route('admin.categories.index') }}" class="flex items-center gap-1.5 p-2 rounded-lg border border-gray-200 hover:border-[#023859] hover:bg-gray-50">
                        <i class="fas fa-tags text-[#023859] text-xs"></i>
                        <span class="text-xs font-medium">Kelola Kategori</span>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-3">
                <h3 class="text-sm font-bold text-[#023859] mb-2">Sistem</h3>
                <ul class="text-xs text-gray-700 space-y-1.5">
                    <li class="flex items-center justify-between"><span>Versi Aplikasi</span><span class="font-semibold">v1.0.0</span></li>
                    <li class="flex items-center justify-between"><span>PHP</span><span class="font-semibold">{{ PHP_VERSION }}</span></li>
                    <li class="flex items-center justify-between"><span>Timezone</span><span class="font-semibold">{{ config('app.timezone') }}</span></li>
                </ul>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/70 p-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-[#023859]">Aktivitas Terbaru</h3>
                    <a href="{{ route('admin.berita.index') }}" class="text-xs text-[#023859] hover:underline">Lihat semua</a>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="py-2 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-newspaper text-xs"></i></span>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Berita baru dipublikasikan</p>
                                <p class="text-[10px] text-gray-500">Upacara Peringatan 17 Agustus • Hari ini</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-500">Admin</span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fas fa-calendar-plus text-xs"></i></span>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Agenda ditambahkan</p>
                                <p class="text-[10px] text-gray-500">Rapat Guru • Kemarin</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-500">Wina</span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fas fa-comments text-xs"></i></span>
                            <div>
                                <p class="text-xs font-medium text-gray-900">Komentar baru masuk</p>
                                <p class="text-[10px] text-gray-500">7 komentar belum dibaca</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-500">Sistem</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
