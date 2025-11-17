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
        if (!Schema::hasTable('photo_downloads')) {
            Schema::create('photo_downloads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('photo_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('session_id', 255)->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('downloaded_at')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('photo_id');
                $table->index('user_id');
                $table->index('session_id');
                $table->index('downloaded_at');
                
                // Foreign keys
                $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_downloads');
    }
};
