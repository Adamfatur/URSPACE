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
        // Add UUID to threads
        Schema::table('threads', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable()->unique();
        });

        // Backfill UUIDs for existing threads
        $threads = \DB::table('threads')->get();
        foreach ($threads as $thread) {
            \DB::table('threads')->where('id', $thread->id)->update(['uuid' => \Str::uuid()]);
        }

        // Make UUID not nullable after backfill
        Schema::table('threads', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        // Add Post features
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('thread_id')->constrained('posts')->onDelete('cascade');
            $table->boolean('is_pinned')->default(false)->after('content');
            $table->enum('status', ['active', 'hidden', 'deleted'])->default('active')->after('is_pinned');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_pinned', 'status', 'deleted_at']);
        });
    }
};
