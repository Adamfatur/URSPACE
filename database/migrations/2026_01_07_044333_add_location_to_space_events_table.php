<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('space_events', function (Blueprint $table) {
            $table->enum('location_type', ['online', 'offline', 'hybrid'])->default('offline')->after('visibility');
            $table->text('location_detail')->nullable()->after('location_type');
        });
    }

    public function down(): void
    {
        Schema::table('space_events', function (Blueprint $table) {
            $table->dropColumn(['location_type', 'location_detail']);
        });
    }
};
