<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Make user_id nullable WITHOUT requiring doctrine/dbal by using raw SQL
        try {
            if (Schema::hasColumn('photo_comments', 'user_id')) {
                DB::statement('ALTER TABLE photo_comments MODIFY user_id BIGINT UNSIGNED NULL');
            }
        } catch (\Throwable $e) {
            // ignore if already nullable or driver does not support this exact syntax
        }

        // 2) Add guest fields if they do not exist
        Schema::table('photo_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('photo_comments', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('photo_comments', 'last_name')) {
                $table->string('last_name', 100)->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('photo_comments', 'email')) {
                $table->string('email', 150)->nullable()->after('last_name');
            }
        });

        // 3) Ensure index on photo_id exists
        try {
            DB::statement('CREATE INDEX photo_comments_photo_id_index ON photo_comments (photo_id)');
        } catch (\Throwable $e) {
            // likely already exists; ignore
        }
    }

    public function down(): void
    {
        // Drop index if exists
        try {
            DB::statement('DROP INDEX photo_comments_photo_id_index ON photo_comments');
        } catch (\Throwable $e) {}

        Schema::table('photo_comments', function (Blueprint $table) {
            if (Schema::hasColumn('photo_comments', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('photo_comments', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('photo_comments', 'email')) {
                $table->dropColumn('email');
            }
        });

        // Attempt to set NOT NULL back (optional)
        try {
            DB::statement('ALTER TABLE photo_comments MODIFY user_id BIGINT UNSIGNED NOT NULL');
        } catch (\Throwable $e) {}
    }
};
