<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PetugasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $petugas = Petugas::select('id', 'username')->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data petugas berhasil diambil',
                'data' => $petugas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data petugas: ' . $e->getMessage()
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
                'username' => 'required|string|max:100|unique:petugas,username',
                'password' => 'required|string|min:6|max:50',
            ], [
                'username.required' => 'Username wajib diisi',
                'username.unique' => 'Username sudah digunakan',
                'username.max' => 'Username maksimal 100 karakter',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter',
                'password.max' => 'Password maksimal 50 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $petugas = Petugas::create([
                'username' => $request->username,
                'password' => $request->password, // Otomatis di-hash oleh model
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Petugas berhasil ditambahkan',
                'data' => [
                    'id' => $petugas->id,
                    'username' => $petugas->username
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan petugas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $petugas = Petugas::select('id', 'username')->find($id);
            
            if (!$petugas) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Petugas tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data petugas berhasil diambil',
                'data' => $petugas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data petugas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $petugas = Petugas::find($id);
            
            if (!$petugas) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Petugas tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:100|unique:petugas,username,' . $id,
                'password' => 'nullable|string|min:6|max:50',
            ], [
                'username.required' => 'Username wajib diisi',
                'username.unique' => 'Username sudah digunakan',
                'username.max' => 'Username maksimal 100 karakter',
                'password.min' => 'Password minimal 6 karakter',
                'password.max' => 'Password maksimal 50 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'username' => $request->username,
            ];

            // Update password hanya jika diisi
            if ($request->filled('password')) {
                $updateData['password'] = $request->password; // Otomatis di-hash oleh model
            }

            $petugas->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Petugas berhasil diperbarui',
                'data' => [
                    'id' => $petugas->id,
                    'username' => $petugas->username
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui petugas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $petugas = Petugas::find($id);
            
            if (!$petugas) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Petugas tidak ditemukan'
                ], 404);
            }

            $petugas->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Petugas berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus petugas: ' . $e->getMessage()
            ], 500);
        }
    }
}