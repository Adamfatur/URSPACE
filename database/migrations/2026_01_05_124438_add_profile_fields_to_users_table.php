<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Academic Info
            $table->string('nim')->nullable()->after('avatar');
            $table->string('program_studi')->nullable()->after('nim');
            $table->string('fakultas')->nullable()->after('program_studi');
            $table->integer('angkatan')->nullable()->after('fakultas');

            // Professional Info
            $table->string('headline')->nullable()->after('bio');
            $table->string('location')->nullable()->after('headline');
            $table->string('website')->nullable()->after('location');
            $table->string('linkedin_url')->nullable()->after('website');
            $table->string('github_url')->nullable()->after('linkedin_url');

            // Open to Work
            $table->boolean('is_open_to_work')->default(false)->after('github_url');
            $table->json('open_to_work_types')->nullable()->after('is_open_to_work');

            // Profile Completion Tracking
            $table->timestamp('profile_completed_at')->nullable()->after('open_to_work_types');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nim',
                'program_studi',
                'fakultas',
                'angkatan',
                'headline',
                'location',
                'website',
                'linkedin_url',
                'github_url',
                'is_open_to_work',
                'open_to_work_types',
                'profile_completed_at'
            ]);
        });
    }
};
