<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $messages = Message::select('id', 'name', 'email', 'message', 'status', 'created_at', 'updated_at')
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data messages berhasil diambil',
                'data' => $messages,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data messages: ' . $e->getMessage(),
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
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000',
                'status' => 'nullable|in:unread,read',
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'message.required' => 'Pesan wajib diisi',
                'status.in' => 'Status harus unread atau read',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $message = Message::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'status' => $request->status ?? 'unread',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message berhasil ditambahkan',
                'data' => $message,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $message = Message::select('id', 'name', 'email', 'message', 'status', 'created_at', 'updated_at')
                ->find($id);

            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data message berhasil diambil',
                'data' => $message,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $messageModel = Message::find($id);

            if (!$messageModel) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message tidak ditemukan',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000',
                'status' => 'required|in:unread,read',
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'message.required' => 'Pesan wajib diisi',
                'status.required' => 'Status wajib diisi',
                'status.in' => 'Status harus unread atau read',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $messageModel->update([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message berhasil diperbarui',
                'data' => $messageModel,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $message = Message::find($id);

            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message tidak ditemukan',
                ], 404);
            }

            $message->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Message berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus message: ' . $e->getMessage(),
            ], 500);
        }
    }
}


