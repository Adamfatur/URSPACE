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
        Schema::table('reports', function (Blueprint $table) {
            $table->tinyInteger('ai_priority_score')->nullable()->after('status');
            $table->string('ai_suggested_action')->nullable()->after('ai_priority_score');
            $table->text('ai_analysis')->nullable()->after('ai_suggested_action');
            $table->tinyInteger('ai_confidence')->nullable()->after('ai_analysis');
            $table->timestamp('ai_analyzed_at')->nullable()->after('ai_confidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'ai_priority_score',
                'ai_suggested_action',
                'ai_analysis',
                'ai_confidence',
                'ai_analyzed_at'
            ]);
        });
    }
};
