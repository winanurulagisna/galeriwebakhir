@extends('admin.layouts.app')

@section('title', 'Kelola Pesan - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Kelola Pesan')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Daftar Pesan</h2>
            <p class="text-gray-600">Kelola semua pesan yang masuk dari pengunjung website</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export Pesan
            </button>
        </div>
    </div>

    <!-- Pesan List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @if($pesan->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pengirim
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pesan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pesan as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-orange-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name ?? 'Anonymous' }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->email ?? 'No email' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $item->title ?? 'No title' }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($item->message ?? 'No message', 100) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Belum Dibaca
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->created_at ?? now() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <!-- Empty State -->
        <div class="px-6 py-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-envelope text-6xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Belum ada pesan</h3>
                <p class="text-gray-600">Pesan dari pengunjung website akan muncul di sini</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Fitur Pesan</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Halaman ini akan menampilkan pesan yang masuk dari form kontak di website. Fitur ini akan diimplementasikan segera dengan:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Melihat detail pesan lengkap</li>
                        <li>Menandai pesan sebagai sudah dibaca</li>
                        <li>Membalas pesan via email</li>
                        <li>Menghapus pesan yang tidak diperlukan</li>
                        <li>Export pesan ke file Excel/PDF</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
