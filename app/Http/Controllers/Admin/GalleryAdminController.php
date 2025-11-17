<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryAdminController extends Controller
{
    public function index()
    {
        // Sembunyikan album dengan kategori 'berita' dari daftar galeri admin
        // Filter semua variasi: 'berita', 'Berita Sekolah', dll
        $galleries = Gallery::withCount('photos')
            ->whereNotIn('category', ['berita', 'Berita Sekolah'])
            ->latest()
            ->paginate(12);
            
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show(Gallery $gallery)
    {
        $gallery->load('photos');
        return view('admin.galleries.show', compact('gallery'));
    }

    public function edit(Gallery $gallery)
    {
        abort(404);
    }

    public function update(Request $request, Gallery $gallery)
    {
        abort(404);
    }

    public function destroy(Gallery $gallery)
    {
        abort(404);
    }
}


