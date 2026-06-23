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
        Schema::create('interests', function (Blueprint $table) {
            $table->string('id', 60)->primary(); // "music_jazz"
            $table->string('category_id', 40); // "music"
            $table->string('label', 100); // "Jazz"
            $table->string('icon', 60)->nullable();  // "MusicalNoteIcon"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });

        Schema::create('interest_categories', function (Blueprint $table) {
            $table->string('id', 40)->primary(); // "music"
            $table->string('label', 80); // "Music"
            $table->string('icon', 60)->nullable(); // "MusicalNoteIcon"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_interests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('interest_id', 60);
            $table->string('category_id', 40); // denormalized — avoids a join in matchmaking
            $table->tinyInteger('weight')->default(1);

            $table->primary(['user_id', 'interest_id']);
            $table->index('interest_id');
            $table->index('category_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('interest_id')->references('id')->on('interests');
            $table->foreign('category_id')->references('id')->on('interest_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
        Schema::dropIfExists('interests');
        Schema::dropIfExists('interest_categories');
    }
};
