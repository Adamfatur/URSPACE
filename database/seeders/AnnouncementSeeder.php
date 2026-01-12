<?php

namespace Database\Seeders;

use App\Models\GlobalAnnouncement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Selamat Datang di Forum UR! 🎉',
                'content' => 'Forum diskusi resmi Universitas Raharja telah hadir. Mari berdiskusi dengan sopan dan saling menghormati.',
                'type' => 'info',
                'is_active' => true,
                'is_dismissible' => true,
                'expires_at' => now()->addMonths(3),
            ],
            [
                'title' => 'Pendaftaran UTS Semester Genap Dibuka',
                'content' => 'Periode pendaftaran UTS 10-20 Januari 2026. Segera daftarkan diri melalui portal akademik.',
                'type' => 'warning',
                'is_active' => true,
                'is_dismissible' => true,
                'expires_at' => now()->addDays(14),
            ],
        ];

        foreach ($announcements as $announcement) {
            GlobalAnnouncement::updateOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}
