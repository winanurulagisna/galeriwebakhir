<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('photo_likes', function (Blueprint $table) {
            // Hapus unique constraint lama yang hanya menggunakan photo_id + session_id
            $table->dropUnique(['photo_id', 'session_id']);
            
            // Tambah unique constraint baru:
            // - Untuk user yang login: photo_id + user_id harus unique
            // - Untuk guest: photo_id + session_id harus unique (ketika user_id null)
            $table->unique(['photo_id', 'user_id', 'session_id'], 'photo_likes_unique');
        });
        
        // Hapus duplikat data yang mungkin sudah ada
        DB::statement("
            DELETE t1 FROM photo_likes t1
            INNER JOIN photo_likes t2 
            WHERE t1.id > t2.id 
            AND t1.photo_id = t2.photo_id 
            AND (
                (t1.user_id IS NOT NULL AND t1.user_id = t2.user_id) OR
                (t1.user_id IS NULL AND t1.session_id = t2.session_id)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_likes', function (Blueprint $table) {
            // Kembalikan ke unique constraint lama
            $table->dropUnique('photo_likes_unique');
            $table->unique(['photo_id', 'session_id']);
        });
    }
};
