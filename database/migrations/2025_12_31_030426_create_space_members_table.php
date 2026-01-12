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
        Schema::create('space_members', function (Blueprint $table) {
            $table->id();
            // Note: Do not add foreign keys here to avoid migration-order issues on MySQL.
            // The FKs will be added in a later MySQL-only migration.
            $table->unsignedBigInteger('space_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->timestamps();

            $table->unique(['space_id', 'user_id']);
            $table->index('space_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_members');
    }
};
