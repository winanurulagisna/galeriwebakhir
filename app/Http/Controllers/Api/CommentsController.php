<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CommentsController extends Controller
{
    public function getApprovedComments(Request $request)
    {
        $photoId = $request->get('photo_id');
        
        if (!$photoId) {
            return response()->json(['error' => 'Photo ID required'], 400);
        }
        
        $comments = [];
        
        // Get approved comments from JSON file
        try {
            $path = storage_path('app/komentar_temp.json');
            if (File::exists($path)) {
                $raw = File::get($path);
                $items = json_decode($raw, true) ?: [];
                
                foreach ($items as $item) {
                    if (($item['status'] ?? '') === 'Disetujui' && 
                        ($item['photo_id'] ?? null) == $photoId) {
                        
                        $comments[] = [
                            'id' => $item['id'] ?? uniqid(),
                            'name' => $item['name'] ?? 'Pengunjung',
                            'body' => $item['body'] ?? '',
                            'created_at' => $item['created_at'] ?? now()->toIso8601String(),
                            'avatar' => strtoupper(substr($item['name'] ?? 'P', 0, 1))
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore errors
        }
        
        // Get approved comments from database
        try {
            if (\Schema::hasTable('photo_comments')) {
                $dbComments = \DB::table('photo_comments')
                    ->leftJoin('users', 'photo_comments.user_id', '=', 'users.id')
                    ->where('photo_comments.photo_id', $photoId)
                    ->where('photo_comments.status', 'approved')
                    ->select(
                        'photo_comments.id',
                        'photo_comments.body',
                        'photo_comments.created_at',
                        'users.name',
                        'photo_comments.first_name',
                        'photo_comments.last_name',
                        'photo_comments.email'
                    )
                    ->orderBy('photo_comments.created_at', 'desc')
                    ->get();
                
                foreach ($dbComments as $comment) {
                    $name = $comment->name ?? trim(($comment->first_name ?? '') . ' ' . ($comment->last_name ?? '')) ?: ($comment->email ?? 'Pengguna');
                    
                    $comments[] = [
                        'id' => 'db_' . $comment->id,
                        'name' => $name,
                        'body' => $comment->body,
                        'created_at' => $comment->created_at,
                        'avatar' => strtoupper(substr($name, 0, 1))
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Ignore errors
        }
        
        // Sort by created_at desc
        usort($comments, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return response()->json([
            'success' => true,
            'comments' => $comments,
            'count' => count($comments)
        ]);
    }
}
