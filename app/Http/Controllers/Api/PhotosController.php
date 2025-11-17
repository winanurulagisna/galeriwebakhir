<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhotosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Photo::with(['gallery' => function($q) {
                $q->select('id', 'post_id', 'position', 'status', 'category', 'title');
            }]);
            
            // Filter by category (from gallery)
            if ($request->has('category') && $request->category != '') {
                $query->whereHas('gallery', function($q) use ($request) {
                    $q->where('category', $request->category);
                });
            }
            
            // Filter by gallery_id
            if ($request->has('gallery_id') && $request->gallery_id != '') {
                $query->where('gallery_id', $request->gallery_id);
            }
            
            // Search by title
            if ($request->has('search') && $request->search != '') {
                $query->where('judul', 'like', '%' . $request->search . '%');
            }
            
            // Get photos with pagination or all
            $perPage = $request->get('per_page', 15);
            $photos = $perPage === 'all' 
                ? $query->orderBy('created_at', 'desc')->get()
                : $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data photos berhasil diambil',
                'data' => $photos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data photos: ' . $e->getMessage()
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
                'gallery_id' => 'required|exists:galleries,id',
                'file' => 'required|string|max:255',
                'judul' => 'required|string|max:100',
            ], [
                'gallery_id.required' => 'Gallery ID wajib diisi',
                'gallery_id.exists' => 'Gallery tidak ditemukan',
                'file.required' => 'File wajib diisi',
                'file.max' => 'File maksimal 255 karakter',
                'judul.required' => 'Judul wajib diisi',
                'judul.max' => 'Judul maksimal 100 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $photo = Photo::create([
                'gallery_id' => $request->gallery_id,
                'file' => $request->file,
                'judul' => $request->judul,
            ]);

            // Load relationship
            $photo->load(['gallery:id,post_id,position,status']);

            return response()->json([
                'status' => 'success',
                'message' => 'Photo berhasil ditambahkan',
                'data' => $photo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $photo = Photo::with(['gallery:id,post_id,position,status'])
                ->select('id', 'gallery_id', 'file', 'judul', 'created_at', 'updated_at')
                ->find($id);
            
            if (!$photo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Photo tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data photo berhasil diambil',
                'data' => $photo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $photo = Photo::find($id);
            
            if (!$photo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Photo tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'gallery_id' => 'required|exists:galleries,id',
                'file' => 'required|string|max:255',
                'judul' => 'required|string|max:100',
            ], [
                'gallery_id.required' => 'Gallery ID wajib diisi',
                'gallery_id.exists' => 'Gallery tidak ditemukan',
                'file.required' => 'File wajib diisi',
                'file.max' => 'File maksimal 255 karakter',
                'judul.required' => 'Judul wajib diisi',
                'judul.max' => 'Judul maksimal 100 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $photo->update([
                'gallery_id' => $request->gallery_id,
                'file' => $request->file,
                'judul' => $request->judul,
            ]);

            // Load relationship
            $photo->load(['gallery:id,post_id,position,status']);

            return response()->json([
                'status' => 'success',
                'message' => 'Photo berhasil diperbarui',
                'data' => $photo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $photo = Photo::find($id);
            
            if (!$photo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Photo tidak ditemukan'
                ], 404);
            }

            // Hapus foto (event deleting di model akan otomatis menghapus data terkait)
            $photo->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Photo berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete photos
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:foto,id'
            ], [
                'ids.required' => 'ID photos wajib diisi',
                'ids.array' => 'ID photos harus berupa array',
                'ids.*.exists' => 'Salah satu photo tidak ditemukan'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Hapus satu per satu agar event deleting terpanggil
            $photos = Photo::whereIn('id', $request->ids)->get();
            $deletedCount = 0;
            
            foreach ($photos as $photo) {
                $photo->delete(); // Event deleting akan otomatis menghapus data terkait
                $deletedCount++;
            }

            return response()->json([
                'status' => 'success',
                'message' => $deletedCount . ' photo berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus photos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bersihkan data orphan (likes, comments, downloads untuk foto yang sudah tidak ada)
     */
    public function cleanOrphanData()
    {
        try {
            $result = Photo::cleanOrphanData();
            
            return response()->json([
                'status' => 'success',
                'message' => $result ? 'Data orphan berhasil dibersihkan' : 'Gagal membersihkan data orphan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membersihkan data orphan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Konversi foto HEIC ke JPEG
     */
    public function convertHeic(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo_id' => 'required|exists:foto,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $photo = Photo::find($request->photo_id);
            $originalPath = public_path($photo->file);
            
            if (!file_exists($originalPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File foto tidak ditemukan'
                ], 404);
            }
            
            $fileExtension = strtolower(pathinfo($photo->file, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, ['heic', 'heif'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File bukan format HEIC/HEIF'
                ], 400);
            }
            
            if (!extension_loaded('imagick')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ImageMagick tidak tersedia untuk konversi HEIC'
                ], 500);
            }
            
            // Konversi ke JPEG
            $jpegPath = preg_replace('/\.(heic|heif)$/i', '.jpg', $originalPath);
            $jpegWebPath = preg_replace('/\.(heic|heif)$/i', '.jpg', $photo->file);
            
            $imagick = new \Imagick($originalPath);
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(85);
            $imagick->writeImage($jpegPath);
            $imagick->clear();
            $imagick->destroy();
            
            // Update database
            $photo->update(['file' => $jpegWebPath]);
            
            // Hapus file HEIC asli
            if (file_exists($originalPath)) {
                unlink($originalPath);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Foto HEIC berhasil dikonversi ke JPEG',
                'data' => [
                    'old_path' => $photo->getOriginal('file'),
                    'new_path' => $jpegWebPath
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal konversi foto HEIC: ' . $e->getMessage()
            ], 500);
        }
    }
}