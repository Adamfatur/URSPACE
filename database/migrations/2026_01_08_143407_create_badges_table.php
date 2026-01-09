<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. "First Post", "Popular", "Helpful"
            $table->string('slug')->unique();
            $table->text('description');     // How to earn the badge
            $table->string('icon');          // Material icon name
            $table->string('color');         // CSS color class (e.g. bg-success, bg-primary)
            $table->string('criteria_type'); // threads_count, likes_count, posts_count, followers_count
            $table->integer('criteria_value'); // threshold value to earn
            $table->integer('points')->default(0); // points awarded
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
