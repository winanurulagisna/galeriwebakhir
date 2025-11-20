<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\PhotoLike;
use App\Models\PostLike;
use App\Models\PhotoDownload;
use App\Models\PhotoComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class UserActivityController extends Controller
{
    /**
     * Show user's comments
     */
    public function comments()
    {
        $user = Auth::user();
        
        // Get user's comments
        $comments = DB::table('photo_comments')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get user's like and download counts
        $likeCount = PhotoLike::where('user_id', $user->id)->count();
        $downloadCount = \Illuminate\Support\Facades\Schema::hasTable('photo_downloads') 
            ? PhotoDownload::where('user_id', $user->id)->count() 
            : 0;
        
        return view('user.comments', compact('comments', 'likeCount', 'downloadCount'));
    }

    /**
     * Show user's downloads
     */
    public function downloads()
    {
        $user = Auth::user();
        
        // fix: tampilkan foto unduhan menggunakan Eloquent dengan relasi yang benar
        $downloads = $user->photoDownloads()
            ->with(['photo.gallery'])
            ->orderByDesc('created_at')
            ->paginate(9);
        
        // Get user's like and comment counts
        $likeCount = PhotoLike::where('user_id', $user->id)->count();
        $commentCount = DB::table('photo_comments')
            ->where('user_id', $user->id)
            ->count();
        
        return view('user.downloads', compact('downloads', 'likeCount', 'commentCount'));
    }

    /**
     * Get user's liked photos AND posts (berita)
     */
    public function getLikedPhotos(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $perPage = 12;
        
        // Get ALL photo likes first (no pagination yet)
        $photoLikesRaw = PhotoLike::where('user_id', $user->id)
            ->with('photo')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get ALL post likes
        $postLikesRaw = PostLike::where('user_id', $user->id)
            ->with('post')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Combine and filter duplicates BEFORE pagination
        // For berita photos: group by related_id (one berita = one entry)
        // For gallery photos: group by photo_id
        $seenItems = [];
        $allLikesUnique = collect();
        
        // Process photo likes
        foreach ($photoLikesRaw as $like) {
            if (!$like->photo) {
                continue; // Skip if photo doesn't exist
            }
            
            // If this is a berita photo, use related_id as unique key
            if ($like->photo->related_type === 'berita' && $like->photo->related_id) {
                $uniqueKey = 'berita_' . $like->photo->related_id;
            } else {
                // For gallery photos, use photo_id
                $uniqueKey = 'photo_' . $like->photo->id;
            }
            
            // Skip if already seen
            if (isset($seenItems[$uniqueKey])) {
                continue;
            }
            
            $seenItems[$uniqueKey] = true;
            $allLikesUnique->push($like);
        }
        
        // Process post likes
        foreach ($postLikesRaw as $like) {
            if (!$like->post) {
                continue; // Skip if post doesn't exist
            }
            
            // For posts, use post_id as unique key
            $uniqueKey = 'berita_' . $like->post->id;
            
            // Skip if already seen
            if (isset($seenItems[$uniqueKey])) {
                continue;
            }
            
            $seenItems[$uniqueKey] = true;
            // Create a mock PhotoLike object for the post
            $mockLike = new \stdClass();
            $mockLike->photo_id = $like->post->id;
            $mockLike->photo = $like->post;
            $mockLike->created_at = $like->created_at;
            $allLikesUnique->push($mockLike);
        }
        
        // Sort by created_at descending
        $allLikesUnique = $allLikesUnique->sortByDesc('created_at')->values();
        
        // Manual pagination after filtering
        $total = $allLikesUnique->count();
        $currentPage = $page;
        $offset = ($currentPage - 1) * $perPage;
        $allLikes = $allLikesUnique->slice($offset, $perPage)->values();
        $hasMore = ($offset + $perPage) < $total;
        
        if ($request->ajax()) {
            // Transform to return photo/post objects (no need to track seen again, already filtered)
            $transformedPhotos = $allLikes->map(function($like) {
                // Try to get photo first
                $photo = \App\Models\Photo::find($like->photo_id);
                
                // If not a photo, check if it's a post (berita)
                if (!$photo) {
                    $post = \App\Models\Post::find($like->photo_id);
                    if (!$post) return null; // Skip if neither photo nor post exists
                    
                    // Convert post to photo-like object for display
                    $photo = (object)[
                        'id' => $post->id,
                        'judul' => $post->judul,
                        'caption' => $post->ringkasan ?? '',
                        'file_path' => $post->image,
                        'is_berita' => true,
                        'berita_url' => '/berita/' . $post->id,
                        'berita_title' => $post->judul,
                        'related_type' => 'berita',
                        'related_id' => $post->id,
                    ];
                }
                
                // Check if this is a berita photo
                $isBeritaPhoto = ($photo->related_type ?? null) === 'berita';
                
                // Get file_path properly
                $filePath = $photo->file_path ?? $photo->file ?? null;
                
                if ($filePath) {
                    $photo->optimal_url = \App\Helpers\ImageUrlHelper::getSafeImageUrl($filePath);
                    $photo->is_heic = str_ends_with(strtolower($filePath), '.heic');
                } else {
                    $photo->optimal_url = '/images/placeholder.svg';
                    $photo->is_heic = false;
                }
                
                // Add berita info if applicable
                if ($isBeritaPhoto && $photo->related_id) {
                    $photo->is_berita = true;
                    $photo->berita_url = '/berita/' . $photo->related_id;
                    
                    // Get berita title if not set
                    if (empty($photo->judul)) {
                        $post = \App\Models\Post::find($photo->related_id);
                        if ($post) {
                            $photo->judul = $post->judul;
                            $photo->berita_title = $post->judul;
                        }
                    }
                }
                
                // Set judul if not present
                if (empty($photo->judul)) {
                    if (is_object($photo) && property_exists($photo, 'gallery') && $photo->gallery) {
                        $photo->judul = $photo->caption ?? $photo->gallery->title;
                    } else {
                        $photo->judul = $photo->caption ?? 'Foto';
                    }
                }
                
                return ['photo' => $photo]; // Return in same format as other methods
            })->filter()->values(); // Remove nulls and reindex
            
            return response()->json([
                'success' => true,
                'photos' => $transformedPhotos,
                'hasMore' => $hasMore
            ]);
        }
        
        return $allLikesUnique;
    }

    /**
     * Get user's commented photos and posts (berita)
     */
    public function getCommentedPhotos(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $perPage = 12;
        
        // Get ALL photo comments - including gallery photos and berita photos
        $photoComments = PhotoComment::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('comment_type', 'photo') // Only photo comments
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('photos')
                      ->whereColumn('photos.id', 'photo_comments.photo_id');
            })
            ->with(['photo' => function($q) {
                $q->with('gallery'); // Load gallery without filter
            }])
            ->whereHas('photo') // Just check photo exists
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('photo_id');
        
        // Get post comments (berita comments) - photo_id in this case is actually post_id
        $postComments = PhotoComment::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('comment_type', 'post') // Only post comments
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('posts_new')
                      ->whereColumn('posts_new.id', 'photo_comments.photo_id')
                      ->where('posts_new.status', 'published');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('photo_id');
        
        // Merge both collections
        $allComments = $photoComments->concat($postComments)->sortByDesc('created_at')->values();
        
        if ($request->ajax()) {
            $offset = ($page - 1) * $perPage;
            $comments = $allComments->slice($offset, $perPage);
            
            // Transform comments to include photo data with optimal_url and is_heic
            $transformedPhotos = $comments->map(function($comment) {
                // Check if this is a post comment (berita)
                if ($comment->comment_type === 'post') {
                    // Get post data
                    $post = \App\Models\Post::with('photos')->find($comment->photo_id);
                    if ($post) {
                        // Get first photo from post
                        $firstPhoto = $post->photos->first();
                        
                        // Create photo object for berita
                        $photo = new \stdClass();
                        $photo->id = $post->id;
                        $photo->judul = $post->judul;
                        $photo->is_berita = true;
                        $photo->berita_url = '/berita/' . $post->id;
                        $photo->related_type = 'berita';
                        
                        // Get image from post
                        if ($firstPhoto) {
                            $photo->file_path = $firstPhoto->file_path ?? $firstPhoto->file;
                            $photo->optimal_url = \App\Helpers\ImageUrlHelper::getSafeImageUrl($photo->file_path);
                            $photo->is_heic = str_ends_with(strtolower($photo->file_path), '.heic');
                        } else {
                            $photo->file_path = $post->image ?? '/images/default-berita.jpg';
                            $photo->optimal_url = $photo->file_path;
                            $photo->is_heic = false;
                        }
                        
                        return [
                            'photo' => $photo,
                            'comment' => [
                                'id' => $comment->id,
                                'body' => $comment->body,
                                'created_at' => $comment->created_at
                            ]
                        ];
                    }
                } else {
                    // Regular photo comment
                    $photo = $comment->photo ?? null;
                    if ($photo) {
                        // Check if this is a berita photo
                        $isBeritaPhoto = ($photo->related_type ?? null) === 'berita';
                        
                        // Get file_path properly
                        $filePath = $photo->file_path ?? $photo->file ?? null;
                        
                        if ($filePath) {
                            $photo->optimal_url = \App\Helpers\ImageUrlHelper::getSafeImageUrl($filePath);
                            $photo->is_heic = str_ends_with(strtolower($filePath), '.heic');
                        } else {
                            $photo->optimal_url = '/images/placeholder.svg';
                            $photo->is_heic = false;
                        }
                        
                        // Add berita info if applicable
                        if ($isBeritaPhoto && $photo->related_id) {
                            $photo->is_berita = true;
                            $photo->berita_url = '/berita/' . $photo->related_id;
                        }
                        
                        // Return the comment with photo data
                        return [
                            'photo' => $photo,
                            'comment' => [
                                'id' => $comment->id,
                                'body' => $comment->body,
                                'created_at' => $comment->created_at
                            ]
                        ];
                    }
                }
                return null;
            })->filter(); // Remove null entries
            
            return response()->json([
                'success' => true,
                'photos' => $transformedPhotos->values(),
                'hasMore' => $allComments->count() > ($offset + $perPage)
            ]);
        }
        
        return $allComments;
    }

    /**
     * Get user's downloaded photos and posts (berita)
     */
    public function getDownloadedPhotos(Request $request)
    {
        try {
            $user = Auth::user();
            $page = $request->get('page', 1);
            $perPage = 12;
            
            // Check if photo_downloads table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('photo_downloads')) {
                if ($request->ajax()) {
                    return response()->json([
                        'photos' => [],
                        'hasMore' => false
                    ]);
                }
                return view('user.downloads', ['downloadedPhotos' => collect([])]);
            }
            
            // Get ALL photo downloads first (without strict filters)
            $allDownloads = PhotoDownload::where('user_id', $user->id)
                ->with(['photo.gallery'])
                ->orderBy('created_at', 'desc')
                ->get();
        
            // Filter only photos that exist (include all photos, even without gallery)
            $photoDownloads = $allDownloads->filter(function($download) {
                // Just check if photo exists
                return $download->photo !== null;
            })->unique('photo_id');
            
            $downloadedPhotos = $photoDownloads->values();
            
            if ($request->ajax()) {
                $offset = ($page - 1) * $perPage;
                $photos = $downloadedPhotos->slice($offset, $perPage);
                
                // Transform photos to ensure all required fields are present
                $transformedPhotos = $photos->map(function($download) {
                    $photo = $download->photo;
                    if ($photo) {
                        // Check if this is a berita photo
                        $isBeritaPhoto = ($photo->related_type ?? null) === 'berita';
                        
                        // Get file_path properly
                        $filePath = $photo->file_path ?? $photo->file ?? null;
                        
                        if ($filePath) {
                            $photo->optimal_url = \App\Helpers\ImageUrlHelper::getSafeImageUrl($filePath);
                            $photo->is_heic = str_ends_with(strtolower($filePath), '.heic');
                        } else {
                            $photo->optimal_url = '/images/placeholder.svg';
                            $photo->is_heic = false;
                        }
                        
                        // Add berita info if applicable
                        if ($isBeritaPhoto && $photo->related_id) {
                            $photo->is_berita = true;
                            $photo->berita_url = '/berita/' . $photo->related_id;
                            
                            // Get berita title if not set
                            if (empty($photo->judul)) {
                                $post = \App\Models\Post::find($photo->related_id);
                                if ($post) {
                                    $photo->judul = $post->judul;
                                    $photo->berita_title = $post->judul;
                                }
                            }
                        }
                        
                        // Set judul if not present
                        if (empty($photo->judul)) {
                            $photo->judul = $photo->caption ?? ($photo->gallery ? $photo->gallery->title : 'Foto');
                        }
                    }
                    return $download;
                });
                
                return response()->json([
                    'success' => true,
                    'photos' => $transformedPhotos->values(),
                    'hasMore' => $downloadedPhotos->count() > ($offset + $perPage)
                ]);
            }
            
            return $downloadedPhotos;
            
        } catch (\Exception $e) {
            Log::error('Error in getDownloadedPhotos: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat foto',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return collect();
        }
    }
}