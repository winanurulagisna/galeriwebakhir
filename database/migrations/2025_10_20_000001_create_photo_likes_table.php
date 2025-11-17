<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('photo_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('photo_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id', 100)->index();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['photo_id','session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_likes');
    }
};
