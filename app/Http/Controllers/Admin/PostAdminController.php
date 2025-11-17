<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostAdminController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(12);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::orderBy('judul')->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'date' => 'nullable|date',
            'category_id' => 'nullable|integer|exists:kategori,id'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan');
    }

    public function show(Post $beritum)
    {
        // Route model key will be 'beritum' because resource name is 'berita'
        return view('admin.posts.show', ['post' => $beritum]);
    }

    public function edit(Post $beritum)
    {
        $categories = Category::orderBy('judul')->get();
        return view('admin.posts.edit', ['post' => $beritum, 'categories' => $categories]);
    }

    public function update(Request $request, Post $beritum)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'date' => 'nullable|date',
            'category_id' => 'nullable|integer|exists:kategori,id'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $beritum->update($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui');
    }

    public function destroy(Post $beritum)
    {
        $beritum->delete();
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus');
    }
}


