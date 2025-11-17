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
        Schema::table('photo_comments', function (Blueprint $table) {
            // Add comment_type column to differentiate between photo and post comments
            if (!Schema::hasColumn('photo_comments', 'comment_type')) {
                $table->string('comment_type', 20)->default('photo')->after('photo_id');
            }
            
            // Add status column for comment approval
            if (!Schema::hasColumn('photo_comments', 'status')) {
                $table->string('status', 20)->default('pending')->after('body');
            }
            
            // Add approved column (legacy support)
            if (!Schema::hasColumn('photo_comments', 'approved')) {
                $table->boolean('approved')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_comments', function (Blueprint $table) {
            if (Schema::hasColumn('photo_comments', 'approved')) {
                $table->dropColumn('approved');
            }
            if (Schema::hasColumn('photo_comments', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('photo_comments', 'comment_type')) {
                $table->dropColumn('comment_type');
            }
        });
    }
};
