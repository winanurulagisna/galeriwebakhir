<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PhotoLikeController;
use App\Http\Controllers\PhotoCommentController;
use App\Http\Controllers\PhotoCommentTempJsonController;
use App\Http\Controllers\Admin\PhotoCommentTempController as AdminPhotoCommentTempController;
use App\Http\Controllers\PublicLoginController;
use App\Http\Controllers\PhotoDownloadController;
use App\Http\Controllers\User\UserActivityController;
use App\Http\Controllers\User\UserProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes - Dynamic Content from Database
Route::get('/', [PublicController::class, 'home'])->name('home');

Route::get('/berita', [PublicController::class, 'berita'])->name('berita.index');
Route::get('/berita/{post}', [PublicController::class, 'showBerita'])->name('berita.show');
// Download image of a post and increment views as download counter
Route::get('/berita/{post}/download', [PublicController::class, 'downloadBerita'])->name('berita.download');

Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery.index');
Route::get('/gallery/{gallery}', [PublicController::class, 'showGallery'])->name('gallery.show');
// Aggregated Acara Sekolah album (all acara photos)
Route::get('/gallery-acara-sekolah', [PublicController::class, 'galleryAcara'])->name('gallery.acara');

// Photo like toggle (session-based)
Route::post('/photos/{photo}/like', [PhotoLikeController::class, 'toggle'])->name('photo.like');

// Post (Berita) like toggle (session-based)
Route::post('/posts/{post}/like', [\App\Http\Controllers\PostLikeController::class, 'toggle'])->name('post.like');
Route::get('/posts/{post}/likes/count', [\App\Http\Controllers\PostLikeController::class, 'count'])->name('post.likes.count');

// Photo download - Auth & Verified Required
Route::get('/photos/{photo}/download', [PhotoDownloadController::class, 'download'])
    ->middleware(['auth', 'verified'])
    ->name('photo.download');

// Photo comments - Auth & Verified Required
Route::post('/photos/{photo}/comments', [PhotoCommentTempJsonController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('photo.comment.store');

// Post (Berita) comments - Auth & Verified Required
Route::post('/posts/{post}/comments', [\App\Http\Controllers\PostCommentController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('post.comment.store');
Route::get('/posts/{post}/comments', [\App\Http\Controllers\PostCommentController::class, 'index'])->name('post.comments.index');

Route::get('/ekstrakurikuler', [PublicController::class, 'ekstrakurikuler'])->name('ekstrakurikuler.index');
Route::get('/ekstrakurikuler/{ekstrakurikuler}', [PublicController::class, 'showEkstrakurikuler'])->name('ekstrakurikuler.show');

Route::get('/profil', [PublicController::class, 'profil'])->name('profil.index');

Route::get('/agenda', [PublicController::class, 'agenda'])->name('agenda.index');

Route::get('/kontak', [PublicController::class, 'kontak'])->name('kontak.index');

// Search Route
Route::get('/search', [PublicController::class, 'search'])->name('search.index');

// Message Route
Route::post('/message', [PublicController::class, 'storeMessage'])->name('message.store');

// API Routes for real-time comments
Route::get('/api/comments', [\App\Http\Controllers\Api\CommentsController::class, 'getApprovedComments'])->name('api.comments');

// Test CSRF token
Route::get('/test-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver')
    ]);
});

// Admin Routes disabled - moved to public/admin PHP dashboard

require __DIR__.'/auth.php';

// Public (separate) login routes for site users (not admin dashboard)
Route::middleware('guest')->group(function () {
    Route::get('/login-public', [PublicLoginController::class, 'show'])->name('public.login');
    Route::post('/login-public', [PublicLoginController::class, 'login'])->name('public.login.post');
});

Route::post('/logout-public', [PublicLoginController::class, 'logout'])->middleware('auth')->name('public.logout');

// User Activity Routes - Auth & Verified Required
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user/comments', [UserActivityController::class, 'comments'])->name('user.comments');
    Route::get('/user/downloads', [UserActivityController::class, 'downloads'])->name('user.downloads');
    
    // AJAX endpoints for profile activity
    Route::get('/user/activity/likes', [UserActivityController::class, 'getLikedPhotos'])->name('user.activity.likes');
    Route::get('/user/activity/comments', [UserActivityController::class, 'getCommentedPhotos'])->name('user.activity.comments');
    Route::get('/user/activity/downloads', [UserActivityController::class, 'getDownloadedPhotos'])->name('user.activity.downloads');
    
    // User Profile Routes
    Route::get('/user/profile', [UserProfileController::class, 'show'])->name('user.profile.show');
    Route::get('/user/profile/edit', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::get('/user/profile/change-password', [UserProfileController::class, 'editPassword'])->name('user.profile.change-password');
    Route::put('/user/profile/password', [UserProfileController::class, 'updatePassword'])->name('user.profile.update-password');
    Route::get('/user/profile/notification', [UserProfileController::class, 'notification'])->name('user.profile.notification');
    Route::get('/user/profile/shipping-address', [UserProfileController::class, 'shippingAddress'])->name('user.profile.shipping-address');
});

// Admin Komentar Foto (Database)
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('/comments', [\App\Http\Controllers\Admin\PhotoCommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{comment}/approve', [\App\Http\Controllers\Admin\PhotoCommentController::class, 'approve'])->name('comments.approve');
    Route::post('/comments/{comment}/reject', [\App\Http\Controllers\Admin\PhotoCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\Admin\PhotoCommentController::class, 'destroy'])->name('comments.destroy');
    
    // Legacy JSON moderation (backup)
    Route::get('/komentar-foto', [AdminPhotoCommentTempController::class, 'index'])->name('komentar.index');
    Route::get('/komentar-foto/list', [AdminPhotoCommentTempController::class, 'list'])->name('komentar.list');
    Route::post('/komentar-foto/{id}/approve', [AdminPhotoCommentTempController::class, 'approve'])->name('komentar.approve');
    Route::post('/komentar-foto/{id}/reject', [AdminPhotoCommentTempController::class, 'reject'])->name('komentar.reject');
    Route::delete('/komentar-foto/{id}', [AdminPhotoCommentTempController::class, 'destroy'])->name('komentar.destroy');
});
