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
        Schema::table('threads', function (Blueprint $table) {
            $table->tinyInteger('ai_moderation_score')->nullable()->after('is_pinned');
            $table->json('ai_moderation_flags')->nullable()->after('ai_moderation_score');
            $table->json('ai_suggested_tags')->nullable()->after('ai_moderation_flags');
            $table->boolean('ai_flagged')->default(false)->after('ai_suggested_tags');
            $table->timestamp('ai_moderated_at')->nullable()->after('ai_flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn([
                'ai_moderation_score',
                'ai_moderation_flags',
                'ai_suggested_tags',
                'ai_flagged',
                'ai_moderated_at'
            ]);
        });
    }
};
