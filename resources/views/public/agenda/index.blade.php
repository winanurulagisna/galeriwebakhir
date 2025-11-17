@extends('public.layouts.app')

@section('title', 'Agenda Sekolah - SMKN 4 KOTA BOGOR')

@section('styles')
    <style>
        .agenda-card {
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .agenda-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(2, 56, 89, 0.15);
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-akan-datang {
            background: #E0F2FE;
            color: #075985;
            border: 1px solid #BAE6FD;
        }
        .status-berlangsung {
            background: #DBEAFE;
            color: #1E40AF;
            border: 1px solid #93C5FD;
        }
        .status-selesai {
            background: #F1F5F9;
            color: #475569;
            border: 1px solid #CBD5E1;
        }
        .agenda-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #023859 0%, #1E3A8A 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .agenda-meta-icon {
            color: #023859;
            opacity: 0.7;
        }
    </style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-4">
    <!-- Header -->
    <div class="text-center mb-6">
        <h1 class="text-xl md:text-2xl font-bold mb-2 text-[#023859]">Agenda Sekolah</h1>
        <p class="text-sm md:text-base text-gray-600 max-w-2xl mx-auto">Jadwal kegiatan dan acara penting SMKN 4 KOTA BOGOR</p>
    </div>

    <!-- Agenda Grid -->
    @if($agenda && $agenda->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($agenda as $item)
                @php
                    $statusClass = match($item->status) {
                        'Selesai' => 'status-selesai',
                        'Berlangsung' => 'status-berlangsung',
                        'Akan Datang' => 'status-akan-datang',
                        default => 'status-akan-datang'
                    };
                    $tanggalFormatted = $item->tanggal->locale('id')->isoFormat('D MMMM YYYY');
                @endphp
                <div class="agenda-card bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Card Header with Icon -->
                    <div class="p-4 pb-3">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="agenda-icon">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-[#023859] mb-1.5 line-clamp-2">{{ $item->judul }}</h3>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $item->status }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Meta Info -->
                        <div class="space-y-1.5 mb-3">
                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                <svg class="w-3.5 h-3.5 agenda-meta-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-medium">{{ $tanggalFormatted }}</span>
                            </div>
                            @if($item->lokasi)
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 agenda-meta-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="line-clamp-1">{{ $item->lokasi }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Description -->
                        @if($item->deskripsi)
                            <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">{{ $item->deskripsi }}</p>
                        @endif
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                            <svg class="w-3.5 h-3.5 text-[#023859] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="agenda-icon mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#023859] mb-1.5">Belum Ada Agenda</h3>
                <p class="text-sm text-gray-500">Belum ada agenda sekolah yang tersedia saat ini.</p>
            </div>
        </div>
    @endif
</div>
@endsection
