<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Defined Users
        $users = [
            [
                'name' => 'Admin Global',
                'username' => 'global_admin',
                'email' => 'admin@raharja.info',
                'role' => 'global_admin',
                'password' => 'password123',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Universitas',
                'username' => 'univ_admin',
                'email' => 'univ@raharja.info',
                'role' => 'univ_admin',
                'password' => 'password123',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Jurusan TI',
                'username' => 'dept_admin_ti',
                'email' => 'dept.ti@raharja.info',
                'role' => 'dept_admin',
                'password' => 'password123',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Jurusan SI',
                'username' => 'dept_admin_si',
                'email' => 'dept.si@raharja.info',
                'role' => 'dept_admin',
                'password' => 'password123',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Moderator Forum',
                'username' => 'moderator',
                'email' => 'mod@raharja.info',
                'role' => 'moderator',
                'password' => 'password123',
                'email_verified_at' => now(),
            ],
        ];

        $credentialText = "CREDENTIALS (GENERATED)\n=======================\n\n";

        foreach ($users as $userData) {
            $password = $userData['password'];
            unset($userData['password']);
            
            User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make($password),
                ])
            );

            $credentialText .= "Role: " . ucfirst($userData['role']) . "\n";
            $credentialText .= "Email: " . $userData['email'] . "\n";
            $credentialText .= "Password: " . $password . "\n";
            $credentialText .= "------------------------\n";
        }

        // Random Users (simulated students)
        User::factory(20)->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Call other seeders
        $this->call([
            SettingsSeeder::class,
            BadgeSeeder::class,
            SpaceSeeder::class,
            ThreadSeeder::class,
            AnnouncementSeeder::class,
        ]);

        // Seed Categories
        $categories = [
            ['name' => 'Akademik', 'slug' => 'akademik', 'icon' => 'school', 'description' => 'Diskusi seputar perkuliahan, tugas, dan akademik'],
            ['name' => 'Sosial', 'slug' => 'sosial', 'icon' => 'groups', 'description' => 'Diskusi umum dan kehidupan sosial kampus'],
            ['name' => 'Berita & Pengumuman', 'slug' => 'berita-pengumuman', 'icon' => 'campaign', 'description' => 'Informasi resmi dan pengumuman kampus'],
            ['name' => 'Alumni', 'slug' => 'alumni', 'icon' => 'workspace_premium', 'description' => 'Forum khusus alumni Universitas Raharja'],
            ['name' => 'Beasiswa', 'slug' => 'beasiswa', 'icon' => 'card_membership', 'description' => 'Informasi beasiswa dan bantuan pendidikan'],
            ['name' => 'Lowongan Kerja', 'slug' => 'lowongan-kerja', 'icon' => 'work', 'description' => 'Info magang, part-time, dan karir'],
            ['name' => 'Kehidupan Kampus', 'slug' => 'kehidupan-kampus', 'icon' => 'local_cafe', 'description' => 'Tips dan cerita seputar kehidupan kampus'],
            ['name' => 'Organisasi & UKM', 'slug' => 'organisasi-ukm', 'icon' => 'account_tree', 'description' => 'Informasi kegiatan organisasi dan UKM'],
            ['name' => 'Event & Lomba', 'slug' => 'event-lomba', 'icon' => 'event', 'description' => 'Info event, seminar, workshop, dan kompetisi'],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'icon' => 'sports_soccer', 'description' => 'Diskusi seputar olahraga dan klub olahraga'],
            ['name' => 'Seni & Budaya', 'slug' => 'seni-budaya', 'icon' => 'palette', 'description' => 'Forum seni, musik, dan kebudayaan'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'icon' => 'psychology', 'description' => 'Diskusi teknologi, programming, dan gadget'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // Seed Tags
        $tags = [
            'Raharja', 'Kuliah', 'Event', 'Tips', 'Mahasiswa', 
            'Ujian', 'Skripsi', 'Magang', 'Volunteer', 'Networking', 
            'Tutorial', 'Programming', 'Design', 'Career', 'Scholarship',
            'Remote', 'Freelance', 'Startup', 'Community', 'Workshop'
        ];
        foreach ($tags as $tag) {
            \App\Models\Tag::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($tag)],
                ['name' => $tag]
            );
        }

        // Seed Forum Rules
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
            \App\Models\ForumRule::updateOrCreate(
                ['title' => $rule['title']],
                $rule
            );
        }

        // Save Credentials
        file_put_contents(base_path('credentials.txt'), $credentialText);
        
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📄 Credentials saved to credentials.txt');
    }
}
