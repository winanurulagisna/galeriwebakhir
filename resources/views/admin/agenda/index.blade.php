@extends('admin.layouts.app')

@section('title', 'Kelola Agenda - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Kelola Agenda')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Daftar Agenda Sekolah</h2>
            <p class="text-gray-600">Kelola semua agenda dan acara sekolah</p>
        </div>
        <a href="{{ route('admin.agenda.create') }}"
           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Agenda
        </a>
    </div>

    <!-- Agenda List -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Agenda
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Acara
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($agenda as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->body, 100) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-day text-purple-500 mr-2"></i>
                                {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                if ($item->created_at) {
                                    $eventDate = \Carbon\Carbon::parse($item->created_at);
                                    $now = \Carbon\Carbon::now();
                                    $status = $eventDate->isPast() ? 'Selesai' : ($eventDate->isToday() ? 'Hari Ini' : 'Akan Datang');
                                    $statusColor = $eventDate->isPast() ? 'gray' : ($eventDate->isToday() ? 'green' : 'blue');
                                } else {
                                    $status = 'N/A';
                                    $statusColor = 'gray';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                <i class="fas fa-circle mr-1 text-xs"></i>
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.agenda.edit', $item->id) }}"
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.agenda.destroy', $item->id) }}"
                                      class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada agenda</p>
                                <p class="text-sm">Mulai dengan menambahkan agenda pertama</p>
                                <a href="{{ route('admin.agenda.create') }}"
                                   class="inline-flex items-center mt-3 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Agenda Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($agenda->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $agenda->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
