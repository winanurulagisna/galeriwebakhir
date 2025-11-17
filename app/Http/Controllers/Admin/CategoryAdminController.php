<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(12);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:100|unique:kategori_new,judul',
        ]);

        Category::create([
            'judul' => $validated['judul']
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function show(Category $kategori)
    {
        return view('admin.categories.show', ['category' => $kategori]);
    }

    public function edit(Category $kategori)
    {
        return view('admin.categories.edit', ['category' => $kategori]);
    }

    public function update(Request $request, Category $kategori)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:100|unique:kategori_new,judul,' . $kategori->id,
        ]);

        $kategori->update([
            'judul' => $validated['judul']
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $kategori)
    {
        $kategori->delete();
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}


