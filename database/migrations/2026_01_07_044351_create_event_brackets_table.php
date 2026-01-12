<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Main Bracket Table
        Schema::create('event_brackets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained('space_events')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('max_participants')->default(16); // Max 64
            $table->enum('status', ['registration', 'ongoing', 'completed'])->default('registration');
            $table->timestamps();
        });

        // Bracket Participants
        Schema::create('event_bracket_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bracket_id')->constrained('event_brackets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Nullable for TBD/Bye
            $table->string('name')->nullable(); // Can have custom name if user is not registered
            $table->unsignedSmallInteger('seed')->nullable(); // Seeding position
            $table->timestamps();
        });

        // Bracket Matches
        Schema::create('event_bracket_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bracket_id')->constrained('event_brackets')->cascadeOnDelete();
            $table->unsignedTinyInteger('round'); // 1 = First Round, 2 = Second, etc.
            $table->unsignedSmallInteger('match_order'); // Order within the round
            $table->foreignId('participant_1_id')->nullable()->constrained('event_bracket_participants')->nullOnDelete();
            $table->foreignId('participant_2_id')->nullable()->constrained('event_bracket_participants')->nullOnDelete();
            $table->foreignId('winner_id')->nullable()->constrained('event_bracket_participants')->nullOnDelete();
            $table->string('score_1')->nullable();
            $table->string('score_2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_bracket_matches');
        Schema::dropIfExists('event_bracket_participants');
        Schema::dropIfExists('event_brackets');
    }
};
