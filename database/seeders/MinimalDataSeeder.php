<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\SpaceMember;
use App\Models\Category;
use App\Models\Tag;
use App\Models\ForumRule;
use App\Models\GlobalAnnouncement;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MinimalDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n=== SEEDING DATA MINIMAL ===\n\n";

        // Get admin user
        $admin = User::where('role', 'global_admin')->first();
        $users = User::where('role', 'user')->take(5)->get();

        // Seed Categories
        echo "→ Seeding Categories...\n";
        $categories = [
            ['name' => 'Akademik', 'slug' => 'akademik', 'icon' => 'school'],
            ['name' => 'Sosial', 'slug' => 'sosial', 'icon' => 'groups'],
            ['name' => 'Berita & Pengumuman', 'slug' => 'berita-pengumuman', 'icon' => 'campaign'],
            ['name' => 'Alumni', 'slug' => 'alumni', 'icon' => 'workspace_premium'],
            ['name' => 'Beasiswa', 'slug' => 'beasiswa', 'icon' => 'card_membership'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'icon' => 'psychology'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
        echo "  ✓ " . count($categories) . " Categories created\n";

        // Seed Tags
        echo "→ Seeding Tags...\n";
        $tags = [
            'Raharja', 'Kuliah', 'Event', 'Tips', 'Mahasiswa', 
            'Ujian', 'Skripsi', 'Magang', 'Volunteer', 'Networking'
        ];
        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($tag)],
                ['name' => $tag]
            );
        }
        echo "  ✓ " . count($tags) . " Tags created\n";

        // Seed Forum Rules
        echo "→ Seeding Forum Rules...\n";
        $rules = [
            [
                'title' => 'Saling Menghormati',
                'content' => 'Hormati sesama anggota forum. Tidak diperbolehkan adanya pelecehan, intimidasi, atau diskriminasi dalam bentuk apapun.',
                'order' => 1
            ],
            [
                'title' => 'Tidak Ada Spam',
                'content' => 'Dilarang mengirimkan pesan berulang, iklan tanpa izin, atau konten yang tidak relevan dengan topik diskusi.',
                'order' => 2
            ],
            [
                'title' => 'Gunakan Bahasa yang Baik',
                'content' => 'Gunakan bahasa Indonesia yang sopan dan mudah dimengerti. Hindari penggunaan kata-kata kasar, menyinggung SARA, atau ujaran kebencian.',
                'order' => 3
            ],
            [
                'title' => 'Konten Berkualitas',
                'content' => 'Pastikan thread yang dibuat memiliki nilai informasi dan tidak melanggar hak cipta orang lain. Sertakan sumber jika mengutip.',
                'order' => 4
            ],
            [
                'title' => 'Patuhi Aturan Kampus',
                'content' => 'Setiap interaksi harus tetap menjunjung tinggi nilai-nilai dan peraturan Universitas Raharja serta norma akademik.',
                'order' => 5
            ],
            [
                'title' => 'Privasi dan Keamanan',
                'content' => 'Jangan membagikan informasi pribadi orang lain tanpa izin. Lindungi privasi diri sendiri dan sesama anggota.',
                'order' => 6
            ],
            [
                'title' => 'Laporkan Pelanggaran',
                'content' => 'Jika menemukan konten yang melanggar aturan, gunakan fitur Report. Tim moderator akan menindaklanjuti laporan.',
                'order' => 7
            ],
        ];

        foreach ($rules as $rule) {
            ForumRule::updateOrCreate(
                ['title' => $rule['title']],
                $rule
            );
        }
        echo "  ✓ " . count($rules) . " Forum Rules created\n";

        // Seed Spaces
        echo "→ Seeding Spaces...\n";
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
                'name' => 'English Club',
                'slug' => 'english-club',
                'description' => 'Practice your English here! Discussion, sharing, and learning together.',
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

            // Add some users
            if ($users->count() > 0) {
                foreach ($users->random(min(3, $users->count())) as $user) {
                    SpaceMember::updateOrCreate(
                        ['space_id' => $space->id, 'user_id' => $user->id],
                        ['role' => 'member']
                    );
                }
            }
        }
        echo "  ✓ " . count($spaces) . " Spaces created\n";

        // Seed Badges
        echo "→ Seeding Badges...\n";
        $badges = [
            ['name' => 'Pendatang Baru', 'slug' => 'pendatang-baru', 'description' => 'Selamat datang di Forum UR!', 'icon' => 'waving_hand', 'color' => '#4CAF50', 'criteria_type' => 'registration', 'criteria_value' => 1],
            ['name' => 'Pembicara Aktif', 'slug' => 'pembicara-aktif', 'description' => 'Membuat 10 thread diskusi.', 'icon' => 'forum', 'color' => '#2196F3', 'criteria_type' => 'threads_count', 'criteria_value' => 10],
            ['name' => 'Influencer', 'slug' => 'influencer', 'description' => 'Mendapatkan 50 likes pada kontenmu.', 'icon' => 'thumb_up', 'color' => '#F44336', 'criteria_type' => 'likes_received', 'criteria_value' => 50],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['name' => $badge['name']],
                $badge
            );
        }
        echo "  ✓ " . count($badges) . " Badges created\n";

        echo "\n✅ Semua data berhasil di-seed!\n\n";
    }
}
