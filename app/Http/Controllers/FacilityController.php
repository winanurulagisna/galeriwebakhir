<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    /**
     * GET /api/facilities - Ambil semua data fasilitas
     */
    public function index()
    {
        try {
            $facilities = Facility::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data fasilitas berhasil diambil',
                'data' => $facilities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/facilities - Buat fasilitas baru
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'deskripsi' => 'required|string',
                'foto' => 'required|string|max:255'
            ]);

            $facility = Facility::create([
                'name' => $request->name,
                'deskripsi' => $request->deskripsi,
                'foto' => $request->foto
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Fasilitas berhasil dibuat',
                'data' => $facility
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
                'message' => 'Gagal membuat fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/facilities/{id} - Ambil data fasilitas berdasarkan ID
     */
    public function show($id)
    {
        try {
            $facility = Facility::findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data fasilitas berhasil diambil',
                'data' => $facility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fasilitas tidak ditemukan'
            ], 404);
        }
    }

    /**
     * PUT /api/facilities/{id} - Update data fasilitas
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'deskripsi' => 'required|string',
                'foto' => 'required|string|max:255'
            ]);

            $facility = Facility::findOrFail($id);
            
            $facility->update([
                'name' => $request->name,
                'deskripsi' => $request->deskripsi,
                'foto' => $request->foto
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Fasilitas berhasil diupdate',
                'data' => $facility
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
                'message' => 'Gagal mengupdate fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/facilities/{id} - Hapus data fasilitas
     */
    public function destroy($id)
    {
        try {
            $facility = Facility::findOrFail($id);
            
            // Hapus foto jika ada
            if ($facility->foto && Storage::disk('public')->exists($facility->foto)) {
                Storage::disk('public')->delete($facility->foto);
            }
            
            $facility->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Fasilitas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/facilities/search/{keyword} - Cari fasilitas berdasarkan nama atau deskripsi
     */
    public function search($keyword)
    {
        try {
            $facilities = Facility::where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('deskripsi', 'LIKE', '%' . $keyword . '%')
                ->get();
            
            if ($facilities->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada fasilitas yang ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pencarian fasilitas berhasil',
                'data' => $facilities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari fasilitas: ' . $e->getMessage()
            ], 500);
        }
    }
}
