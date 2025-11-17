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
        Schema::table('photo_likes', function (Blueprint $table) {
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('photo_likes', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('photo_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_likes', function (Blueprint $table) {
            if (Schema::hasColumn('photo_likes', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
