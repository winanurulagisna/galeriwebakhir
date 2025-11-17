<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller

{
    public function index()
    {
        // ambil semua user dari tabel users
        $user = User::all();

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

     public function store(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $user
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
            'message' => 'Gagal membuat user: ' . $e->getMessage()
        ], 500);
    }
}

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:user,email,' . $id . ',id_user',
                'password' => 'nullable|string|min:6',
            ]);

            $user = User::findOrFail($id);
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            
            // Update password hanya jika diisi
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }
            
            $user->update($updateData);
            
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diupdate',
                'data' => $user
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
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }
}
