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
        Schema::table('galleries', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('galleries', 'title')) {
                $table->string('title', 150)->after('id');
            }
            if (!Schema::hasColumn('galleries', 'caption')) {
                $table->text('caption')->after('title');
            }
            if (!Schema::hasColumn('galleries', 'file_path')) {
                $table->string('file_path', 255)->after('caption');
            }
            
            // Remove status_temp column if it exists
            if (Schema::hasColumn('galleries', 'status_temp')) {
                try {
                    $table->dropColumn('status_temp');
                } catch (\Exception $e) {
                    // Column might not exist, continue
                }
            }
            
            // Ensure status column is correct type
            try {
                $table->string('status', 20)->default('draft')->change();
            } catch (\Exception $e) {
                // Column might already be correct type
            }
            
            // Ensure category column exists
            if (!Schema::hasColumn('galleries', 'category')) {
                $table->string('category', 50)->default('general')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            //
        });
    }
};
