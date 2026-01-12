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
            // Note: Do not add foreign key here to avoid migration-order issues on MySQL.
            // The FK will be added in a later MySQL-only migration.
            if (!Schema::hasColumn('threads', 'space_id')) {
                $table->unsignedBigInteger('space_id')->nullable()->after('category_id');
                $table->index('space_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (Schema::hasColumn('threads', 'space_id')) {
                // FK (if any) is removed in the dedicated FK migration's down().
                $table->dropIndex(['space_id']);
                $table->dropColumn('space_id');
            }
        });
    }
};
