<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Main Event Vote (Poll) Table
        Schema::create('event_votes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained('space_events')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_anonymous')->default(false); // Secret voting
            $table->boolean('is_active')->default(true);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // Vote Options
        Schema::create('event_vote_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_vote_id')->constrained('event_votes')->cascadeOnDelete();
            $table->string('option_text');
            $table->timestamps();
        });

        // Vote Responses (who voted for what)
        Schema::create('event_vote_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_vote_id')->constrained('event_votes')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('event_vote_options')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['event_vote_id', 'user_id']); // One vote per user per poll
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_vote_responses');
        Schema::dropIfExists('event_vote_options');
        Schema::dropIfExists('event_votes');
    }
};
