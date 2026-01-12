<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpaceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'global_admin')->first();
        $users = User::where('role', 'user')->get();

        $spaces = [
            [
                'name' => 'Sistem Informasi',
                'slug' => 'sistem-informasi',
                'description' => 'Komunitas mahasiswa dan alumni program studi Sistem Informasi. Berbagi materi kuliah, tips skripsi, dan informasi akademik.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'Teknik Informatika',
                'slug' => 'teknik-informatika',
                'description' => 'Wadah diskusi untuk mahasiswa Teknik Informatika. Bahas coding, algoritma, dan project bersama.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'Desain Grafis',
                'slug' => 'desain-grafis',
                'description' => 'Komunitas untuk pecinta desain. Share portofolio, tips design, dan kritik karya.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'Himpunan Mahasiswa',
                'slug' => 'himpunan-mahasiswa',
                'description' => 'Space resmi Himpunan Mahasiswa Universitas Raharja. Informasi kegiatan dan program kerja.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'Komunitas Gaming',
                'slug' => 'komunitas-gaming',
                'description' => 'Buat para gamers Raharja! Cari tim, diskusi game, dan info turnamen.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'English Club',
                'slug' => 'english-club',
                'description' => 'Practice your English here! Discussion, sharing, and learning together.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'Startup & Entrepreneur',
                'slug' => 'startup-entrepreneur',
                'description' => 'Komunitas untuk mahasiswa yang tertarik membangun bisnis dan startup.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'Alumni Network',
                'slug' => 'alumni-network',
                'description' => 'Jaringan alumni Universitas Raharja. Networking, lowongan kerja, dan berbagi pengalaman.',
                'is_private' => true,
                'status' => 'approved',
            ],
            [
                'name' => 'Research & Development',
                'slug' => 'research-development',
                'description' => 'Diskusi tentang penelitian, jurnal, dan pengembangan ilmu pengetahuan.',
                'is_private' => false,
                'status' => 'approved',
            ],
            [
                'name' => 'Photography Club',
                'slug' => 'photography-club',
                'description' => 'Berbagi hasil foto, tips fotografi, dan diskusi seputar kamera.',
                'is_private' => false,
                'status' => 'approved',
            ],
        ];

        foreach ($spaces as $spaceData) {
            $space = Space::updateOrCreate(
                ['slug' => $spaceData['slug']],
                array_merge($spaceData, ['owner_id' => $admin->id])
            );

            // Add owner as admin member
            SpaceMember::updateOrCreate(
                ['space_id' => $space->id, 'user_id' => $admin->id],
                ['role' => 'admin']
            );

            // Add some random members
            $randomUsers = $users->random(min(5, $users->count()));
            foreach ($randomUsers as $user) {
                SpaceMember::updateOrCreate(
                    ['space_id' => $space->id, 'user_id' => $user->id],
                    ['role' => 'member']
                );
            }
        }
    }
}
