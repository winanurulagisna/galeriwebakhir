<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GalleriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $galleries = Gallery::with(['post:id,judul'])
                ->select('id', 'post_id', 'position', 'status', 'created_at', 'updated_at')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data galleries berhasil diambil',
                'data' => $galleries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data galleries: ' . $e->getMessage()
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
                'post_id' => 'nullable|exists:posts,id',
                'position' => 'required|integer',
                'status' => 'required|integer',
            ], [
                'post_id.exists' => 'Post tidak ditemukan',
                'position.required' => 'Position wajib diisi',
                'position.integer' => 'Position harus berupa angka',
                'status.required' => 'Status wajib diisi',
                'status.integer' => 'Status harus berupa angka',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $gallery = Gallery::create([
                'post_id' => $request->post_id,
                'position' => $request->position,
                'status' => $request->status,
            ]);

            // Load relationship
            $gallery->load(['post:id,judul']);

            return response()->json([
                'status' => 'success',
                'message' => 'Gallery berhasil ditambahkan',
                'data' => $gallery
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan gallery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $gallery = Gallery::with(['post:id,judul'])
                ->select('id', 'post_id', 'position', 'status', 'created_at', 'updated_at')
                ->find($id);
            
            if (!$gallery) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gallery tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data gallery berhasil diambil',
                'data' => $gallery
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data gallery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $gallery = Gallery::find($id);
            
            if (!$gallery) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gallery tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'post_id' => 'nullable|exists:posts,id',
                'position' => 'required|integer',
                'status' => 'required|integer',
            ], [
                'post_id.exists' => 'Post tidak ditemukan',
                'position.required' => 'Position wajib diisi',
                'position.integer' => 'Position harus berupa angka',
                'status.required' => 'Status wajib diisi',
                'status.integer' => 'Status harus berupa angka',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $gallery->update([
                'post_id' => $request->post_id,
                'position' => $request->position,
                'status' => $request->status,
            ]);

            // Load relationship
            $gallery->load(['post:id,judul']);

            return response()->json([
                'status' => 'success',
                'message' => 'Gallery berhasil diperbarui',
                'data' => $gallery
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui gallery: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $gallery = Gallery::find($id);
            
            if (!$gallery) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gallery tidak ditemukan'
                ], 404);
            }

            $gallery->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Gallery berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus gallery: ' . $e->getMessage()
            ], 500);
        }
    }
}