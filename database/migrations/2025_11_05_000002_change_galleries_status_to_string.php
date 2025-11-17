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
        // First, add a temporary column
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('status_temp', 20)->nullable()->after('status');
        });
        
        // Copy and convert data: 0 = published, 1 = draft, 'draft' = draft, 'published' = published
        \DB::statement("UPDATE galleries SET status_temp = CASE 
            WHEN status = 0 OR status = '0' THEN 'published'
            WHEN status = 1 OR status = '1' THEN 'draft'
            WHEN status = 'published' THEN 'published'
            WHEN status = 'draft' THEN 'draft'
            ELSE 'draft'
        END");
        
        // Drop old column and rename temp column
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('galleries', function (Blueprint $table) {
            $table->renameColumn('status_temp', 'status');
        });
        
        // Set default value
        \DB::statement("ALTER TABLE galleries MODIFY status VARCHAR(20) DEFAULT 'draft' NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert string back to integer
        \DB::statement("UPDATE galleries SET status = 0 WHERE status = 'published'");
        \DB::statement("UPDATE galleries SET status = 1 WHERE status = 'draft'");
        
        Schema::table('galleries', function (Blueprint $table) {
            $table->integer('status')->default(0)->change();
        });
    }
};
