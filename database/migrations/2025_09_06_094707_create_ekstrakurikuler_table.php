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
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 150)->unique();
            $table->text('description');
            $table->string('image', 255)->nullable();
            $table->string('icon', 50)->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->integer('members')->default(0);
            $table->string('color', 20)->default('blue');
            $table->text('benefits')->nullable();
            $table->string('schedule', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('instructor', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler');
    }
};
