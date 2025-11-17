<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        try {
            $pages = Page::all();
            
            return response()->json([
                'status' => 'success',
                'data' => $pages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pages: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:150',
                'body' => 'required|string',
                'status' => 'required|in:draft,published,archived',
                'foto' => 'nullable|string|max:255',
            ]);

            // Generate slug dari title
            $slug = Str::slug($request->title);
            
            // Pastikan slug unik
            $counter = 1;
            $originalSlug = $slug;
            while (Page::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $page = Page::create([
                'title' => $request->title,
                'slug' => $slug,
                'body' => $request->body,
                'status' => $request->status,
                'foto' => $request->foto,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Page berhasil dibuat',
                'data' => $page
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $page = Page::findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $page
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Page tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:150',
                'body' => 'required|string',
                'status' => 'required|in:draft,published,archived',
                'foto' => 'nullable|string|max:255',
            ]);

            $page = Page::findOrFail($id);
            
            // Generate slug baru jika title berubah
            $slug = Str::slug($request->title);
            if ($slug !== $page->slug) {
                // Pastikan slug unik
                $counter = 1;
                $originalSlug = $slug;
                while (Page::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $page->update([
                'title' => $request->title,
                'slug' => $slug,
                'body' => $request->body,
                'status' => $request->status,
                'foto' => $request->foto,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Page berhasil diupdate',
                'data' => $page
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $page = Page::findOrFail($id);
            $page->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Page berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBySlug($slug)
    {
        try {
            $page = Page::where('slug', $slug)->first();
            
            if (!$page) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Page tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $page
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getByStatus($status)
    {
        try {
            // Validasi status yang diizinkan
            if (!in_array($status, ['draft', 'published', 'archived'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Status tidak valid. Gunakan: draft, published, atau archived'
                ], 400);
            }

            $pages = Page::where('status', $status)->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $pages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil pages: ' . $e->getMessage()
            ], 500);
        }
    }
} 