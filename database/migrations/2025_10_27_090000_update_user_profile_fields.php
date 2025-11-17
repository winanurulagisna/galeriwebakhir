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
        Schema::table('users', function (Blueprint $table) {
            // Drop unique index on username if exists
            try {
                $table->dropUnique('users_username_unique');
            } catch (\Throwable $e) {
                // ignore if index doesn't exist
            }
        });

        // Modify enum to include 'other' without requiring doctrine/dbal
        try {
            DB::statement("ALTER TABLE users MODIFY gender ENUM('male','female','other') NULL");
        } catch (\Throwable $e) {
            // ignore if DB driver doesn't support this (will be fine if already modified)
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert gender back to ['male','female'] and re-add unique on username
        try {
            DB::statement("ALTER TABLE users MODIFY gender ENUM('male','female') NULL");
        } catch (\Throwable $e) {
            // ignore
        }

        Schema::table('users', function (Blueprint $table) {
            try {
                $table->unique('username');
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};
