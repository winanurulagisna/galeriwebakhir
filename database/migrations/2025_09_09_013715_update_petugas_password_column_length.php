<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            // Mengubah kolom password dari varchar(50) menjadi varchar(255)
            // untuk menampung hash password Laravel yang lebih panjang
            $table->string('password', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            // Kembalikan ke ukuran semula jika rollback
            $table->string('password', 50)->change();
        });
    }
};