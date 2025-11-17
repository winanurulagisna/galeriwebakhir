<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\Api\PetugasController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\Api\PhotosController;
use App\Http\Controllers\Api\GalleriesController;
use App\Http\Controllers\Api\ProfilesController;
use App\Http\Controllers\Api\MessagesController;

Route::get('/test', function () {
    return response()->json(['message' => 'API jalan 🚀']);
});

#route user
Route::get('/user', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

#route pages
Route::get('/pages', [PageController::class, 'index']);
Route::post('/pages', [PageController::class, 'store']);
Route::get('/pages/{id}', [PageController::class, 'show']);
Route::put('/pages/{id}', [PageController::class, 'update']);
Route::delete('/pages/{id}', [PageController::class, 'destroy']);
Route::get('/pages/slug/{slug}', [PageController::class, 'getBySlug']);
Route::get('/pages/status/{status}', [PageController::class, 'getByStatus']);


#route facilities
Route::get('/facilities', [FacilityController::class, 'index']);
Route::post('/facilities', [FacilityController::class, 'store']);
Route::get('/facilities/{id}', [FacilityController::class, 'show']);
Route::put('/facilities/{id}', [FacilityController::class, 'update']);
Route::delete('/facilities/{id}', [FacilityController::class, 'destroy']);
Route::get('/facilities/search/{keyword}', [FacilityController::class, 'search']);

# Note: Routes untuk categories, posts, dan galleries sudah dipindahkan ke API resource routes di bawah

#route messages
Route::apiResource('messages', MessagesController::class);

#route petugas
Route::apiResource('petugas', PetugasController::class);

#route kategori
Route::apiResource('kategori', KategoriController::class);

#route posts
Route::apiResource('posts', PostsController::class);

#route photos
Route::apiResource('photos', PhotosController::class);
Route::post('/photos/bulk-delete', [PhotosController::class, 'bulkDelete']);
Route::post('/photos/clean-orphan-data', [PhotosController::class, 'cleanOrphanData']);
Route::post('/photos/convert-heic', [PhotosController::class, 'convertHeic']);

#route galleries
Route::apiResource('galleries', GalleriesController::class);

#route profiles
Route::apiResource('profiles', ProfilesController::class);
