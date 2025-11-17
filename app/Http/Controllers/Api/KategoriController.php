<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $kategori = Category::select('id', 'judul', 'created_at', 'updated_at')->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data kategori berhasil diambil',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kategori: ' . $e->getMessage()
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
                'judul' => 'required|string|max:255|unique:kategori,judul',
            ], [
                'judul.required' => 'Judul wajib diisi',
                'judul.unique' => 'Judul sudah digunakan',
                'judul.max' => 'Judul maksimal 255 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kategori = Category::create([
                'judul' => $request->judul,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $kategori
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $kategori = Category::select('id', 'judul', 'created_at', 'updated_at')->find($id);
            
            if (!$kategori) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data kategori berhasil diambil',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $kategori = Category::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255|unique:kategori,judul,' . $id,
            ], [
                'judul.required' => 'Judul wajib diisi',
                'judul.unique' => 'Judul sudah digunakan',
                'judul.max' => 'Judul maksimal 255 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kategori->update([
                'judul' => $request->judul,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil diperbarui',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $kategori = Category::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            $kategori->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Kategori berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }
}