@extends('public.layouts.app')

@section('title', 'Acara Sekolah - Galeri')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-10">
        <h1 class="gallery-title">Acara Sekolah</h1>
        <p class="gallery-subtitle">Kumpulan foto dari acara sekolah yang dikelola melalui Admin</p>
    </div>

    @if(isset($photos) && $photos->count())
        <div class="album-grid">
            @foreach($photos as $photo)
            @php
                $file = $photo->file ?? null;
                $src = $file ? \App\Helpers\ImageUrlHelper::getSafeImageUrl($file) : asset('images/default-gallery.jpg');
            @endphp
            <a href="{{ $src }}" class="gallery-card block glightbox" data-gallery="acara-sekolah" data-title="{{ $photo->judul ?? 'Acara Sekolah' }}">
                <div class="gallery-thumb">
                    <img src="{{ $src }}" alt="{{ $photo->judul ?? 'Acara' }}" class="gallery-img">
                </div>
                <h3 class="gallery-title-below">{{ $photo->judul ?: 'Foto Acara' }}</h3>
            </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $photos->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-images text-gray-400 text-6xl mb-6"></i>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum ada foto acara</h3>
            <p class="text-gray-600">Silakan tambahkan foto melalui Admin &gt; Acara Sekolah.</p>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
.gallery-title { color:#023859; font-weight:800; font-size:1.75rem; }
.gallery-subtitle { color:#64748b; font-size:0.98rem; }
.album-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 1rem; }
@media (min-width:768px){ .album-grid{ grid-template-columns: repeat(3, minmax(0,1fr)); } }
@media (min-width:1024px){ .album-grid{ grid-template-columns: repeat(4, minmax(0,1fr)); } }
.gallery-card { display:flex; flex-direction:column; gap:.5rem; background:#fff; border-radius:14px; box-shadow:0 6px 14px rgba(2,56,89,.06); padding:.6rem; border:1px solid rgba(2,56,89,.08); }
.gallery-thumb { position:relative; aspect-ratio:1/1; border-radius:10px; overflow:hidden; background:#e5e7eb; }
.gallery-img { width:100%; height:100%; object-fit:cover; transition: transform .3s ease; display:block; }
.gallery-thumb:hover .gallery-img { transform: scale(1.05); }
.gallery-title-below { margin:.1rem 0 0; font-size:.95rem; font-weight:600; color:#023859; }
</style>
@endsection
