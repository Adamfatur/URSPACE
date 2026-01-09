<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Forum UR', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Forum diskusi resmi Universitas Raharja', 'type' => 'string', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'admin@forumur.id', 'type' => 'string', 'group' => 'general'],

            // SEO
            ['key' => 'seo_default_title', 'value' => 'Forum UR - Forum Diskusi Universitas Raharja', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo_default_description', 'value' => 'Tempat berbagi pengalaman, berdiskusi, dan berkolaborasi bagi Civitas Akademika Universitas Raharja.', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo_keywords', 'value' => 'forum, universitas raharja, diskusi, mahasiswa', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo_robots_index', 'value' => '1', 'type' => 'boolean', 'group' => 'seo'],

            // Forum
            ['key' => 'forum_threads_per_page', 'value' => '15', 'type' => 'integer', 'group' => 'forum'],
            ['key' => 'forum_auto_hide_threshold', 'value' => '5', 'type' => 'integer', 'group' => 'forum'],
            ['key' => 'forum_require_approval', 'value' => '0', 'type' => 'boolean', 'group' => 'forum'],

            // Features
            ['key' => 'feature_pwa_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'feature_badges_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'feature_analytics_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'feature_notifications_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
