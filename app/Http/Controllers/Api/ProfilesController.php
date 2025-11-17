<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $profiles = Profile::select('id', 'judul', 'isi', 'created_at', 'updated_at')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data profiles berhasil diambil',
                'data' => $profiles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data profiles: ' . $e->getMessage()
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
            ], [
                'judul.required' => 'Judul wajib diisi',
                'judul.max' => 'Judul maksimal 150 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $profile = Profile::create([
                'judul' => $request->judul,
                'isi' => $request->isi,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil ditambahkan',
                'data' => $profile
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $profile = Profile::select('id', 'judul', 'isi', 'created_at', 'updated_at')
                ->find($id);
            
            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profile tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data profile berhasil diambil',
                'data' => $profile
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $profile = Profile::find($id);
            
            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profile tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:150',
                'isi' => 'nullable|string',
            ], [
                'judul.required' => 'Judul wajib diisi',
                'judul.max' => 'Judul maksimal 150 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $profile->update([
                'judul' => $request->judul,
                'isi' => $request->isi,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil diperbarui',
                'data' => $profile
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $profile = Profile::find($id);
            
            if (!$profile) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profile tidak ditemukan'
                ], 404);
            }

            $profile->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus profile: ' . $e->getMessage()
            ], 500);
        }
    }
}