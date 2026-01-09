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
            ],
            [
                'name' => 'Admin Universitas',
                'username' => 'univ_admin',
                'email' => 'univ@raharja.info',
                'role' => 'univ_admin',
                'password' => 'password123',
            ],
            [
                'name' => 'Admin Jurusan',
                'username' => 'dept_admin',
                'email' => 'dept@raharja.info',
                'role' => 'dept_admin',
                'password' => 'password123',
            ],
            [
                'name' => 'Moderator',
                'username' => 'moderator_01',
                'email' => 'mod@raharja.info',
                'role' => 'moderator',
                'password' => 'password123',
            ],
            [
                'name' => 'Mahasiswa 1',
                'username' => 'mahasiswa_01',
                'email' => 'mhs1@raharja.info',
                'role' => 'user',
                'password' => 'password123',
            ],
        ];

        $credentialText = "CREDENTIALS (GENERATED)\n=======================\n\n";

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'role' => $userData['role'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            $credentialText .= "Role: " . ucfirst($userData['role']) . "\n";
            $credentialText .= "Email: " . $userData['email'] . "\n";
            $credentialText .= "Password: " . $userData['password'] . "\n";
            $credentialText .= "------------------------\n";
        }

        // Random Users
        User::factory(10)->create();

        // Seed Categories
        $categories = [
            ['name' => 'Akademik', 'icon' => 'school'],
            ['name' => 'Sosial', 'icon' => 'groups'],
            ['name' => 'Berita & Pengumuman', 'icon' => 'campaign'],
            ['name' => 'Alumni', 'icon' => 'workspace_premium'],
            ['name' => 'Beasiswa', 'icon' => 'card_membership'],
            ['name' => 'Lowongan Kerja', 'icon' => 'work'],
            ['name' => 'Kehidupan Kampus', 'icon' => 'local_cafe'],
            ['name' => 'Organisasi & UKM', 'icon' => 'account_tree'],
            ['name' => 'Event & Lomba', 'icon' => 'event'],
            ['name' => 'Olahraga', 'icon' => 'sports_soccer'],
            ['name' => 'Seni & Budaya', 'icon' => 'palette'],
            ['name' => 'Teknologi', 'icon' => 'psychology'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::create($cat);
        }

        // Seed Tags
        $tags = ['Raharja', 'Kuliah', 'Event', 'Tips', 'Mahasiswa', 'Ujian', 'Skripsi', 'Magang', 'Volunteer', 'Networking', 'Tutorial'];
        foreach ($tags as $tag) {
            \App\Models\Tag::create(['name' => $tag]);
        }

        // Seed Forum Rules
        $rules = [
            [
                'title' => 'Saling Menghormati',
                'content' => 'Hormati sesama anggota forum. Tidak diperbolehkan adanya pelecehan, intimidasi, atau diskriminasi.',
                'order' => 1
            ],
            [
                'title' => 'Tidak Ada Spam',
                'content' => 'Dilarang mengirimkan pesan berulang, iklan tanpa izin, atau konten yang tidak relevan dengan topik.',
                'order' => 2
            ],
            [
                'title' => 'Gunakan Bahasa yang Baik',
                'content' => 'Gunakan bahasa yang sopan dan mudah dimengerti. Hindari penggunaan kata-kata kasar atau menyinggung.',
                'order' => 3
            ],
            [
                'title' => 'Konten Berkualitas',
                'content' => 'Pastikan thread yang dibuat memiliki manfaat dan tidak melanggar hak cipta orang lain.',
                'order' => 4
            ],
            [
                'title' => 'Patuhi Aturan Kampus',
                'content' => 'Setiap interaksi harus tetap menjunjung tinggi nilai-nilai dan peraturan Universitas Raharja.',
                'order' => 5
            ],
        ];

        foreach ($rules as $rule) {
            \App\Models\ForumRule::create($rule);
        }

        // Save Credentials
        file_put_contents(base_path('credentials.txt'), $credentialText);
    }
}
