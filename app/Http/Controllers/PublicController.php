<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Gallery;
use App\Models\Photo;
use App\Models\Profile;
use App\Models\Message;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    /**
     * Display the home page with dynamic content
     */
    public function home()
    {
        // Get latest posts for news slider (only berita/terkini category)
        $latestPosts = Post::with(['category', 'petugas', 'photos'])
            ->where('status', 'published')
            ->whereHas('category', function($query) {
                $query->where('judul', 'like', '%berita%')
                      ->orWhere('judul', 'like', '%news%')
                      ->orWhere('judul', 'like', '%terkini%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get galleries for gallery section
        $galleries = Gallery::with(['photos'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get random photos for gallery section on homepage
        $randomPhotos = Photo::with(['gallery'])
            ->whereHas('gallery', function($q) {
                $q->where('status', 'published');
            })
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // Get latest 5 photos for hero slider (newest uploaded)
        $latestPhotos = Photo::with(['gallery'])
            ->whereHas('gallery', function($q) {
                $q->where('status', 'published');
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get latest messages
        $messages = Message::orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get active ekstrakurikuler for home page (only ekstrakurikuler category)
        $ekstrakurikuler = Gallery::where('status', 'published')
            ->where('category', 'ekstrakurikuler')
            ->orderBy('title')
            ->limit(6)
            ->get();

        return view('public.home', compact('latestPosts', 'galleries', 'messages', 'ekstrakurikuler', 'randomPhotos', 'latestPhotos'));
    }

    /**
     * Display all news/posts
     */
    public function berita()
    {
        $posts = Post::with(['category', 'petugas', 'photos'])
            ->where('status', 'published')
            ->whereHas('category', function($query) {
                $query->where('judul', 'like', '%berita%')
                      ->orWhere('judul', 'like', '%news%')
                      ->orWhere('judul', 'like', '%terkini%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Check if tables exist before querying
        $likedPostIds = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('photo_likes')) {
            $sessionId = session()->getId();
            $likedPostIds = \App\Models\PostLike::where('session_id', $sessionId)
                ->pluck('photo_id')
                ->toArray();
        }

        // Add likes and comments count if tables exist
        if (\Illuminate\Support\Facades\Schema::hasTable('photo_likes') && 
            \Illuminate\Support\Facades\Schema::hasTable('photo_comments')) {
            $posts->load(['likes', 'comments']);
            foreach ($posts as $post) {
                $post->likes_count = $post->likes->count();
                $post->comments_count = $post->comments()->count();
            }
        } else {
            // Set default counts if tables don't exist
            foreach ($posts as $post) {
                $post->likes_count = 0;
                $post->comments_count = 0;
            }
        }

        $categories = Category::orderBy('judul')->get();

        return view('public.berita.index', compact('posts', 'categories', 'likedPostIds'));
    }

    /**
     * Display a specific post
     */
    public function showBerita(Post $post)
    {
        // Load photos relationship
        $post->load(['photos', 'category', 'petugas']);
        
        // Increment view count
        $post->increment('views');

        $relatedPosts = Post::with(['category', 'photos'])
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('kategori_id', $post->kategori_id)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('public.berita.show', compact('post', 'relatedPosts'));
    }

    /**
     * Download the post primary image and increment download counter
     * Downloads the FIRST PHOTO of the post (not random from gallery)
     */
    public function downloadBerita(Post $post)
    {
        // Get the FIRST photo from THIS specific post
        $photo = $post->photos()->first();

        // If no photo found, use default image
        if (!$photo) {
            $imagePath = '/images/default-berita.jpg';
            $filePath = public_path($imagePath);
            $filename = 'berita-' . Str::slug($post->judul ?: 'unduhan') . '.jpg';
            return response()->download($filePath, $filename);
        }

        // Track download activity in photo_downloads table
        try {
            \App\Models\PhotoDownload::create([
                'photo_id' => $photo->id,
                'user_id' => auth()->id(), // null if guest
                'session_id' => session()->getId(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'downloaded_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't stop the download
            \Log::warning('Failed to log photo download for berita', [
                'post_id' => $post->id,
                'photo_id' => $photo->id,
                'error' => $e->getMessage()
            ]);
        }

        // Increment download counter on post
        $post->increment('downloads_count');

        // Prepare file for download
        $filePath = public_path(ltrim($photo->file_path, '/'));
        
        // Fallback to default if file not found
        if (!file_exists($filePath)) {
            $filePath = public_path('images/default-berita.jpg');
        }

        $filename = 'berita-' . Str::slug($post->judul ?: 'unduhan') . '-' . $photo->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

        return response()->download($filePath, $filename);
    }

    /**
     * Display all galleries
     */
    public function gallery(Request $request)
    {
        // Get category filter from request (optional)
        $categoryFilter = $request->get('category');
        
        // Unified album list including Ekstrakulikuler and Kegiatan Sekolah
        // Note: 'Berita Sekolah' category removed from gallery - berita now has its own page at /berita
        $query = Gallery::with(['photos'])
            ->where('status', 'published')
            ->whereNotIn('category', ['Berita Sekolah', 'berita'])
            // Hide unwanted/placeholder albums from public grid
            ->whereNotIn('title', ['Acara Sekolah', 'Album Acara Sekolah', 'Clasmeet', 'Classmeet', 'Berita Terkini']);
        
        // Apply category filter if provided (for direct URL access)
        if ($categoryFilter) {
            $categoryMap = [
                'acara' => 'Kegiatan Sekolah',
                'kegiatan' => 'Kegiatan Sekolah',
                'ekstrakurikuler' => 'Ekstrakulikuler',
                'berita' => 'Berita Sekolah'
            ];
            $dbCategory = $categoryMap[$categoryFilter] ?? $categoryFilter;
            $query->where('category', $dbCategory);
        }
        
        $albums = $query->orderBy('title')->get();

        // Cover image for synthetic 'Kegiatan Sekolah' album card
        $acaraCover = Photo::whereHas('gallery', function($q){
                $q->where('status', 'published')
                  ->where('category', 'Kegiatan Sekolah');
            })
            ->where('related_type', 'acara')
            ->orderBy('created_at', 'desc')
            ->value('file_path');

        // Find gallery to link: prefer title 'Kegiatan Sekolah' IF it has acara photos; otherwise latest gallery with acara photos
        $acaraGallery = Gallery::where('status', 'published')
            ->where('category', 'Kegiatan Sekolah')
            ->where('title', 'Kegiatan Sekolah')
            ->whereHas('photos', function($q){ $q->where('related_type','acara'); })
            ->first();
        if (!$acaraGallery) {
            $acaraGallery = Gallery::where('status', 'published')
                ->where('category', 'Kegiatan Sekolah')
                ->whereHas('photos', function($q){ $q->where('related_type','acara'); })
                ->orderBy('created_at','desc')
                ->first();
        }

        return view('public.gallery.index', [
            'albums' => $albums,
            'acaraCover' => $acaraCover,
            'acaraGallery' => $acaraGallery,
        ]);
    }

    /**
     * Display a specific gallery
     */
    public function showGallery(Gallery $gallery)
    {
        // Check if tables exist
        $hasLikesTable = \Illuminate\Support\Facades\Schema::hasTable('photo_likes');
        $hasCommentsTable = \Illuminate\Support\Facades\Schema::hasTable('photo_comments');
        $hasDownloadsTable = \Illuminate\Support\Facades\Schema::hasTable('photo_downloads');

        // Load photos with counts from database tables
        $gallery->load([
            'photos' => function($q) use ($hasLikesTable, $hasCommentsTable, $hasDownloadsTable) {
                if ($hasLikesTable) {
                    $q->withCount('likes');
                }
                if ($hasCommentsTable) {
                    $q->with(['comments' => function($qc){
                        $qc->with('user')->latest()->limit(5);
                    }])->withCount('comments');
                }
                if ($hasDownloadsTable) {
                    $q->withCount('downloads');
                }
            },
            'post'
        ]);

        // Determine which photos are liked by current session (only if table exists)
        $sessionId = session()->getId();
        $likedPhotoIds = [];
        if ($hasLikesTable && $gallery->photos && $gallery->photos->count() > 0) {
            $query = \App\Models\PhotoLike::whereIn('photo_id', $gallery->photos->pluck('id'));
            
            // Check for both authenticated users and guests
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            } else {
                $query->where('session_id', $sessionId);
            }
            
            $likedPhotoIds = $query->pluck('photo_id')->toArray();
        }

        $relatedGalleries = Gallery::with(['photos'])
            ->where('status', 'published')
            ->where('id', '!=', $gallery->id)
            ->when($gallery->category, function ($q) use ($gallery) {
                $q->where('category', $gallery->category);
            })
            ->when(isset($gallery->post_id) && $gallery->post_id, function ($q) use ($gallery) {
                $q->where('post_id', $gallery->post_id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        // Load approved JSON comments grouped by photo_id
        $jsonCommentsByPhoto = [];
        try {
            $path = storage_path('app/komentar_temp.json');
            if (File::exists($path)) {
                $raw = File::get($path);
                $items = json_decode($raw, true) ?: [];
                foreach ($items as $it) {
                    if (($it['status'] ?? '') !== 'Disetujui') continue;
                    $pid = $it['photo_id'] ?? null;
                    if (!$pid) continue;
                    $jsonCommentsByPhoto[$pid] = $jsonCommentsByPhoto[$pid] ?? [];
                    $jsonCommentsByPhoto[$pid][] = $it;
                }
            }
        } catch (\Throwable $e) {
            $jsonCommentsByPhoto = [];
        }
        
        return view('public.gallery.show', [
            'gallery' => $gallery,
            'relatedGalleries' => $relatedGalleries,
            'likedPhotoIds' => $likedPhotoIds,
            'hasCommentsTable' => $hasCommentsTable,
            'hasDownloadsTable' => $hasDownloadsTable,
            'jsonCommentsByPhoto' => $jsonCommentsByPhoto,
        ]);
    }

    /**
     * Display aggregated 'Acara Sekolah' album (all acara photos)
     */
    public function galleryAcara(Request $request)
    {
        // Get photos that are linked to acara and belong to galleries categorized as 'acara sekolah'
        $photos = Photo::where('related_type', 'acara')
            ->whereHas('gallery', function($q){
                $q->where('status', 'published')
                  ->where('category', 'acara sekolah');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        return view('public.gallery.acara', [
            'title' => 'Acara Sekolah',
            'photos' => $photos,
        ]);
    }

    /**
     * Display all ekstrakurikuler
     */
    public function ekstrakurikuler()
    {
        $ekstrakurikuler = Gallery::with(['photos'])
            ->where('status', 'published')
            ->where('category', 'ekstrakurikuler')
            ->orderBy('title')
            ->get();

        return view('public.ekstrakurikuler.index', compact('ekstrakurikuler'));
    }

    /**
     * Display a specific ekstrakurikuler
     */
    public function showEkstrakurikuler(Gallery $ekstrakurikuler)
    {
        $ekstrakurikuler->load(['photos']);
        
        $relatedEkstrakurikuler = Gallery::with(['photos'])
            ->where('status', 'published')
            ->where('id', '!=', $ekstrakurikuler->id)
            ->orderBy('title')
            ->limit(4)
            ->get();

        return view('public.ekstrakurikuler.show', compact('ekstrakurikuler', 'relatedEkstrakurikuler'));
    }

    /**
     * Display profile information
     */
    public function profil()
    {
        // Return view with empty profiles collection
        // The view has static content, so no database query needed
        $profiles = collect([]);

        return view('public.profil.index', compact('profiles'));
    }

    /**
     * Display agenda/events from agenda table
     */
    public function agenda()
    {
        // Get all agenda ordered by date (upcoming first, then past)
        $agendaUpcoming = \App\Models\Agenda::where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->get();

        $agendaPast = \App\Models\Agenda::where('tanggal', '<', now()->toDateString())
            ->orderBy('tanggal', 'desc')
            ->limit(10)
            ->get();

        // Merge upcoming and past agenda
        $agenda = $agendaUpcoming->concat($agendaPast);

        return view('public.agenda.index', compact('agenda'));
    }

    /**
     * Display contact page
     */
    public function kontak()
    {
        return view('public.kontak.index');
    }

    /**
     * Store a message from contact form
     */
    public function storeMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
            'rating' => 'nullable|numeric|min:1|max:5'
        ]);

        $data = $request->only(['name','email','message']);
        // Store rating only if the column exists to avoid DB errors
        if (Schema::hasColumn('messages_new', 'rating')) {
            $data['rating'] = $request->input('rating');
        }

        Message::create($data);

        return redirect()->back()->with('success', 'Pesan Anda berhasil dikirim!');
    }

    /**
     * Search functionality
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->back();
        }

        // Search in posts (berita)
        $posts = Post::with(['category', 'photos'])
            ->where('status', 'published')
            ->whereHas('category', function($q) {
                $q->where('judul', 'like', '%berita%')
                  ->orWhere('judul', 'like', '%news%')
                  ->orWhere('judul', 'like', '%terkini%');
            })
            ->where(function($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('isi', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Search in ekstrakurikuler
        $ekstrakurikuler = Gallery::with(['photos'])
            ->where('status', 'published')
            ->where('category', 'ekstrakurikuler')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('caption', 'like', "%{$query}%");
            })
            ->orderBy('title')
            ->limit(10)
            ->get();

        // Search in galleries (search by gallery title, caption, or photo caption)
        $galleries = Gallery::with(['photos'])
            ->where('status', 'published')
            ->where('category', '!=', 'ekstrakurikuler') // Exclude ekstrakurikuler
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('caption', 'like', "%{$query}%")
                  ->orWhereHas('photos', function($photoQuery) use ($query) {
                      $photoQuery->where('caption', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Combine all results into a single collection with type indicator
        $results = collect();
        
        foreach ($posts as $post) {
            $results->push([
                'type' => 'berita',
                'title' => $post->judul,
                'description' => \Illuminate\Support\Str::limit(strip_tags($post->isi), 200),
                'url' => route('berita.show', ['post' => $post->id]),
                'image' => $post->photos->first()->file_path ?? null,
                'date' => $post->created_at,
                'data' => $post
            ]);
        }
        
        foreach ($ekstrakurikuler as $ekskul) {
            $results->push([
                'type' => 'ekstrakurikuler',
                'title' => $ekskul->title,
                'description' => \Illuminate\Support\Str::limit($ekskul->caption ?? '', 200),
                'url' => route('ekstrakurikuler.show', ['ekstrakurikuler' => $ekskul->id]),
                'image' => $ekskul->photos->first()->file_path ?? null,
                'date' => $ekskul->created_at,
                'data' => $ekskul
            ]);
        }
        
        foreach ($galleries as $gallery) {
            $results->push([
                'type' => 'galeri',
                'title' => $gallery->title,
                'description' => \Illuminate\Support\Str::limit($gallery->caption ?? '', 200),
                'url' => route('gallery.show', ['gallery' => $gallery->id]),
                'image' => $gallery->photos->first()->file_path ?? null,
                'date' => $gallery->created_at,
                'data' => $gallery
            ]);
        }
        
        // Sort by date (newest first)
        $results = $results->sortByDesc('date');

        return view('public.search.index', compact('query', 'results', 'posts', 'ekstrakurikuler', 'galleries'));
    }
}
