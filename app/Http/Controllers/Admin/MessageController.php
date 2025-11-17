<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Store a newly created message
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'message.required' => 'Pesan wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create the message
        $message = Message::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'status' => 'unread'
        ]);

        // Redirect back with success message
        return back()->with('success', 'Pesan berhasil dikirim! Terima kasih atas pesan Anda.');
    }

    /**
     * Display messages for admin
     */
    public function index()
    {
        $messages = Message::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Mark message as read
     */
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        $message->update(['status' => 'read']);
        
        return back()->with('success', 'Pesan ditandai sebagai telah dibaca.');
    }

    /**
     * Delete message
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        
        return back()->with('success', 'Pesan berhasil dihapus.');
    }

    /**
     * Get messages for API (public display)
     */
    public function getMessages()
    {
        $messages = Message::orderBy('created_at', 'desc')
                          ->get(['id', 'name', 'email', 'message', 'created_at']);
        
        return response()->json($messages);
    }
}
