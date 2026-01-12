<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('institution');
            $table->string('degree')->nullable(); // S1, S2, D3, SMA, etc.
            $table->string('field_of_study')->nullable();
            $table->integer('start_year');
            $table->integer('end_year')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->text('activities')->nullable(); // Organizations, achievements
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_educations');
    }
};
