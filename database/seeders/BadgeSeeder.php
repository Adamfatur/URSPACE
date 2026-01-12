<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // Engagement Badges
            [
                'name' => 'Pendatang Baru',
                'slug' => 'pendatang-baru',
                'description' => 'Selamat datang di Forum UR! Mulai perjalananmu.',
                'icon' => 'waving_hand',
                'color' => '#4CAF50',
                'criteria_type' => 'registration',
                'criteria_value' => 1,
            ],
            [
                'name' => 'Pembicara Aktif',
                'slug' => 'pembicara-aktif',
                'description' => 'Membuat 10 thread diskusi.',
                'icon' => 'forum',
                'color' => '#2196F3',
                'criteria_type' => 'threads_count',
                'criteria_value' => 10,
            ],
            [
                'name' => 'Kontributor Hebat',
                'slug' => 'kontributor-hebat',
                'description' => 'Membuat 50 thread diskusi.',
                'icon' => 'stars',
                'color' => '#9C27B0',
                'criteria_type' => 'threads_count',
                'criteria_value' => 50,
            ],
            [
                'name' => 'Master Diskusi',
                'slug' => 'master-diskusi',
                'description' => 'Membuat 100 thread diskusi.',
                'icon' => 'military_tech',
                'color' => '#FF9800',
                'criteria_type' => 'threads_count',
                'criteria_value' => 100,
            ],
            [
                'name' => 'Komentator',
                'slug' => 'komentator',
                'description' => 'Memberikan 25 komentar.',
                'icon' => 'comment',
                'color' => '#00BCD4',
                'criteria_type' => 'posts_count',
                'criteria_value' => 25,
            ],
            [
                'name' => 'Ahli Berdiskusi',
                'slug' => 'ahli-berdiskusi',
                'description' => 'Memberikan 100 komentar.',
                'icon' => 'chat',
                'color' => '#E91E63',
                'criteria_type' => 'posts_count',
                'criteria_value' => 100,
            ],
            [
                'name' => 'Influencer',
                'slug' => 'influencer',
                'description' => 'Mendapatkan 50 likes pada kontenmu.',
                'icon' => 'thumb_up',
                'color' => '#F44336',
                'criteria_type' => 'likes_received',
                'criteria_value' => 50,
            ],
            [
                'name' => 'Superstar',
                'slug' => 'superstar',
                'description' => 'Mendapatkan 200 likes pada kontenmu.',
                'icon' => 'star',
                'color' => '#FFD700',
                'criteria_type' => 'likes_received',
                'criteria_value' => 200,
            ],

            // Special Badges
            [
                'name' => 'Anggota Terverifikasi',
                'slug' => 'anggota-terverifikasi',
                'description' => 'Email terverifikasi sebagai civitas Raharja.',
                'icon' => 'verified',
                'color' => '#1DA1F2',
                'criteria_type' => 'email_verified',
                'criteria_value' => 1,
            ],
            [
                'name' => 'Profil Lengkap',
                'slug' => 'profil-lengkap',
                'description' => 'Melengkapi semua informasi profil.',
                'icon' => 'account_circle',
                'color' => '#673AB7',
                'criteria_type' => 'profile_complete',
                'criteria_value' => 1,
            ],
            [
                'name' => 'Pemimpin Komunitas',
                'slug' => 'pemimpin-komunitas',
                'description' => 'Menjadi admin atau moderator Space.',
                'icon' => 'shield',
                'color' => '#3F51B5',
                'criteria_type' => 'space_leader',
                'criteria_value' => 1,
            ],
            [
                'name' => 'Pelopor',
                'slug' => 'pelopor',
                'description' => 'Membuat Space komunitas.',
                'icon' => 'rocket_launch',
                'color' => '#009688',
                'criteria_type' => 'space_created',
                'criteria_value' => 1,
            ],

            // Academic Badges
            [
                'name' => 'Mentor Akademik',
                'slug' => 'mentor-akademik',
                'description' => 'Aktif membantu di kategori Akademik.',
                'icon' => 'school',
                'color' => '#795548',
                'criteria_type' => 'category_activity',
                'criteria_value' => 20,
            ],
            [
                'name' => 'Tech Enthusiast',
                'slug' => 'tech-enthusiast',
                'description' => 'Aktif berkontribusi di kategori Teknologi.',
                'icon' => 'code',
                'color' => '#607D8B',
                'criteria_type' => 'category_activity',
                'criteria_value' => 20,
            ],

            // Anniversary Badges
            [
                'name' => 'Veteran 1 Tahun',
                'slug' => 'veteran-1-tahun',
                'description' => 'Sudah bergabung selama 1 tahun.',
                'icon' => 'cake',
                'color' => '#FF5722',
                'criteria_type' => 'account_age_days',
                'criteria_value' => 365,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['name' => $badge['name']],
                $badge
            );
        }
    }
}
