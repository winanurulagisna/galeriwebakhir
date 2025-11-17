<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posts = Post::with(['category:id,judul', 'petugas:id,username'])
                ->select('id', 'judul', 'isi', 'kategori_id', 'petugas_id', 'status', 'views', 'created_at', 'updated_at')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data posts berhasil diambil',
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data posts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:150',
                'isi' => 'nullable|string',
                'kategori_id' => 'nullable|exists:kategori,id',
                'petugas_id' => 'required|exists:petugas,id',
                'status' => 'required|in:draft,published',
                'views' => 'nullable|integer|min:0',
            ], [
                'judul.required' => 'Judul wajib diisi',
                'judul.max' => 'Judul maksimal 150 karakter',
                'kategori_id.exists' => 'Kategori tidak ditemukan',
                'petugas_id.required' => 'Petugas wajib diisi',
                'petugas_id.exists' => 'Petugas tidak ditemukan',
                'status.required' => 'Status wajib diisi',
                'status.in' => 'Status harus draft atau published',
                'views.integer' => 'Views harus berupa angka',
                'views.min' => 'Views minimal 0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post = Post::create([
                'judul' => $request->judul,
                'isi' => $request->isi,
                'kategori_id' => $request->kategori_id,
                'petugas_id' => $request->petugas_id,
                'status' => $request->status,
                'views' => $request->views ?? 0,
            ]);

            // Load relationships
            $post->load(['category:id,judul', 'petugas:id,username']);

            return response()->json([
                'status' => 'success',
                'message' => 'Post berhasil ditambahkan',
                'data' => $post
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = Post::with(['category:id,judul', 'petugas:id,username'])
                ->select('id', 'judul', 'isi', 'kategori_id', 'petugas_id', 'status', 'views', 'created_at', 'updated_at')
                ->find($id);
            
            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data post berhasil diambil',
                'data' => $post
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $post = Post::find($id);
            
            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:150',
                'isi' => 'nullable|string',
                'kategori_id' => 'nullable|exists:kategori,id',
                'petugas_id' => 'required|exists:petugas,id',
                'status' => 'required|in:draft,published',
                'views' => 'nullable|integer|min:0',
            ], [
                'judul.required' => 'Judul wajib diisi',
                'judul.max' => 'Judul maksimal 150 karakter',
                'kategori_id.exists' => 'Kategori tidak ditemukan',
                'petugas_id.required' => 'Petugas wajib diisi',
                'petugas_id.exists' => 'Petugas tidak ditemukan',
                'status.required' => 'Status wajib diisi',
                'status.in' => 'Status harus draft atau published',
                'views.integer' => 'Views harus berupa angka',
                'views.min' => 'Views minimal 0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post->update([
                'judul' => $request->judul,
                'isi' => $request->isi,
                'kategori_id' => $request->kategori_id,
                'petugas_id' => $request->petugas_id,
                'status' => $request->status,
                'views' => $request->views ?? $post->views,
            ]);

            // Load relationships
            $post->load(['category:id,judul', 'petugas:id,username']);

            return response()->json([
                'status' => 'success',
                'message' => 'Post berhasil diperbarui',
                'data' => $post
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::find($id);
            
            if (!$post) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post tidak ditemukan'
                ], 404);
            }

            $post->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Post berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus post: ' . $e->getMessage()
            ], 500);
        }
    }
}