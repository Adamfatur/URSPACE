<?php

namespace Database\Seeders;

use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThreadSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $threads = [
            // Akademik
            [
                'title' => 'Tips Sukses Mengerjakan Skripsi dalam 3 Bulan',
                'content' => "Halo teman-teman! 👋\n\nSaya ingin berbagi pengalaman saya menyelesaikan skripsi dalam waktu 3 bulan. Semoga bisa membantu kalian yang sedang berjuang.\n\n**1. Tentukan Topik yang Kamu Sukai**\nPilih topik yang benar-benar kamu minati. Ini akan membuatmu lebih semangat dalam proses penelitian.\n\n**2. Buat Timeline yang Realistis**\nPecah target besar menjadi milestone kecil. Misalnya:\n- Minggu 1-2: Pengumpulan referensi\n- Minggu 3-4: Penyusunan BAB 1-2\n- dst.\n\n**3. Konsultasi Rutin dengan Dosen Pembimbing**\nJangan takut untuk bertanya. Dosen pembimbing ada untuk membantu kita.\n\n**4. Jaga Kesehatan Mental**\nIstirahat yang cukup dan jangan lupa refreshing!\n\nAda yang mau menambahkan? 😊",
                'category_slug' => 'akademik',
                'thread_type' => 'discussion',
            ],
            [
                'title' => 'Jadwal UTS Semester Genap 2025/2026',
                'content' => "📢 **PENGUMUMAN JADWAL UTS**\n\nKepada seluruh mahasiswa Universitas Raharja,\n\nBerikut jadwal Ujian Tengah Semester (UTS) Semester Genap TA 2025/2026:\n\n📅 **Periode:** 24 Februari - 7 Maret 2026\n\n⏰ **Waktu:**\n- Sesi 1: 08.00 - 10.00 WIB\n- Sesi 2: 10.30 - 12.30 WIB\n- Sesi 3: 13.30 - 15.30 WIB\n\n📍 **Lokasi:** Sesuai dengan jadwal masing-masing kelas\n\n**Catatan Penting:**\n1. Mahasiswa wajib hadir 15 menit sebelum ujian dimulai\n2. Bawa KTM dan alat tulis\n3. Dilarang membawa HP ke ruang ujian\n\nSemangat belajar! 💪",
                'category_slug' => 'berita-pengumuman',
                'thread_type' => 'article',
            ],
            [
                'title' => 'Lowongan Magang: PT Teknologi Indonesia (Remote)',
                'content' => "🚀 **LOWONGAN MAGANG**\n\n**PT Teknologi Indonesia** membuka kesempatan magang untuk mahasiswa/i dengan posisi:\n\n**1. Frontend Developer Intern**\n- Menguasai React/Vue.js\n- Familiar dengan Git\n- Durasi: 3-6 bulan\n\n**2. Backend Developer Intern**\n- Menguasai PHP/Node.js\n- Familiar dengan database MySQL/PostgreSQL\n- Durasi: 3-6 bulan\n\n**3. UI/UX Design Intern**\n- Menguasai Figma\n- Memiliki portfolio\n- Durasi: 3-6 bulan\n\n**Benefit:**\n✅ Remote/WFH\n✅ Uang saku Rp 2.000.000/bulan\n✅ Sertifikat\n✅ Bisa dikonversi untuk KKP/PKL\n\n📧 Kirim CV ke: hr@teknologiindonesia.com\n📅 Deadline: 31 Januari 2026",
                'category_slug' => 'lowongan-kerja',
                'thread_type' => 'article',
            ],
            [
                'title' => 'Rekomendasi Laptop untuk Mahasiswa IT Budget 10 Juta',
                'content' => "Halo semuanya!\n\nSaya mau tanya, laptop apa yang recommended untuk mahasiswa IT dengan budget sekitar 10 jutaan?\n\nKebutuhan:\n- Coding (VS Code, Android Studio)\n- Design ringan (Figma, Canva)\n- Zoom meeting\n\nSpek minimum yang saya cari:\n- RAM 16GB\n- SSD 512GB\n- Layar 14-15 inch\n\nAda rekomendasi? Terima kasih! 🙏",
                'category_slug' => 'teknologi',
                'thread_type' => 'question',
            ],
            [
                'title' => 'Cara Mendaftar Beasiswa Unggulan Kemendikbud 2026',
                'content' => "**PANDUAN LENGKAP BEASISWA UNGGULAN 2026**\n\n📚 Beasiswa Unggulan Kemendikbud kembali dibuka! Berikut panduan lengkapnya:\n\n**Persyaratan Umum:**\n1. WNI\n2. Mahasiswa aktif semester 3+\n3. IPK minimal 3.25\n4. Tidak sedang menerima beasiswa lain\n\n**Dokumen yang Diperlukan:**\n- KTP & KK\n- Transkrip nilai\n- Surat rekomendasi dari kampus\n- Essay motivasi (500 kata)\n- Sertifikat prestasi (jika ada)\n\n**Timeline:**\n- Pendaftaran: 1-28 Februari 2026\n- Seleksi Administrasi: Maret 2026\n- Pengumuman: April 2026\n\n**Link Pendaftaran:** beasiswaunggulan.kemdikbud.go.id\n\nAda yang pernah dapat beasiswa ini? Share pengalamannya dong! 👇",
                'category_slug' => 'beasiswa',
                'thread_type' => 'article',
            ],
            [
                'title' => 'Event: Workshop Web Development dengan Laravel',
                'content' => "🎉 **WORKSHOP GRATIS!**\n\n**\"Building Modern Web Apps with Laravel 12\"**\n\n📅 Sabtu, 18 Januari 2026\n⏰ 09.00 - 15.00 WIB\n📍 Lab Komputer Gedung B Lt.3\n\n**Yang akan dipelajari:**\n- Setup Laravel project\n- Routing & Controllers\n- Database & Eloquent ORM\n- Authentication\n- Deploy ke production\n\n**Pembicara:**\nJohn Doe - Senior Developer @ Startup ABC\n\n**Fasilitas:**\n✅ Sertifikat\n✅ Snack & Lunch\n✅ Source code\n\n**Kuota:** 30 peserta\n\n📝 Daftar: bit.ly/workshop-laravel-ur\n\nGratis untuk mahasiswa Raharja! 🚀",
                'category_slug' => 'event-lomba',
                'thread_type' => 'article',
            ],
            [
                'title' => 'Pengalaman Ikut Kompetisi Hackathon Nasional',
                'content' => "Hai semua! 👋\n\nMinggu lalu tim saya baru saja selesai ikut Hackathon Nasional yang diadakan oleh Kemenkominfo. Mau share pengalaman nih!\n\n**Persiapan:**\n- Bentuk tim 3 orang dengan skill yang complementary\n- Riset tema kompetisi\n- Siapkan tech stack yang familiar\n\n**Selama Kompetisi (48 jam non-stop):**\n- Bagi task dengan jelas\n- Komunikasi intens via Discord\n- Prioritaskan MVP dulu\n- Jangan lupa istirahat!\n\n**Tips:**\n1. Fokus pada problem solving, bukan teknologi\n2. Presentasi itu penting! Latihan pitch\n3. Network dengan peserta lain\n\nAlhamdulillah kami dapat juara 3! 🏆\n\nAda yang mau ikut hackathon bareng? Let's form a team!",
                'category_slug' => 'event-lomba',
                'thread_type' => 'discussion',
            ],
            [
                'title' => 'Rekomendasi Tempat Nongkrong Dekat Kampus',
                'content' => "Butuh rekomendasi tempat nongkrong yang enak buat ngerjain tugas dekat kampus dong!\n\nKriteria:\n- WiFi kenceng\n- Stop kontak banyak\n- Harga mahasiswa-friendly\n- Bisa duduk lama\n\nShare tempat favorit kalian! ☕",
                'category_slug' => 'kehidupan-kampus',
                'thread_type' => 'question',
            ],
            [
                'title' => 'Cerita Alumni: Dari Kampus ke Unicorn Startup',
                'content' => "**Perjalanan Karir Saya Setelah Lulus dari Raharja**\n\nHalo adik-adik! Saya alumni angkatan 2019. Mau share perjalanan karir saya.\n\n**2019-2023: Masa Kuliah**\n- Aktif di UKM Programming\n- Magang di 2 startup\n- Banyak ikut kompetisi\n\n**2023: Fresh Graduate**\n- Apply ke 50+ perusahaan\n- Dapat 3 offering\n- Pilih startup kecil untuk belajar\n\n**2024: Career Growth**\n- Pindah ke Unicorn startup\n- Posisi: Software Engineer\n- Salary naik 3x lipat\n\n**2025: Sekarang**\n- Lead Engineer\n- Manage tim 5 orang\n- Side project menghasilkan\n\n**Kunci Sukses:**\n1. Bangun portfolio sejak kuliah\n2. Network itu penting!\n3. Never stop learning\n4. Jangan takut gagal\n\nAda pertanyaan? AMA! 🙌",
                'category_slug' => 'alumni',
                'thread_type' => 'article',
            ],
            [
                'title' => 'Info: Pendaftaran UKM Semester Genap Dibuka!',
                'content' => "📢 **PENDAFTARAN UKM SEMESTER GENAP 2025/2026**\n\n🗓️ Periode: 6-20 Januari 2026\n\n**UKM yang Buka Pendaftaran:**\n\n🎨 **UKM Seni**\n- Musik\n- Tari\n- Teater\n\n💻 **UKM Akademik**\n- Programming Club\n- English Club\n- Debat\n\n⚽ **UKM Olahraga**\n- Futsal\n- Basket\n- Badminton\n\n🎮 **UKM Minat**\n- E-Sports\n- Photography\n- Entrepreneur\n\n**Cara Daftar:**\n1. Isi form di: bit.ly/daftar-ukm-ur\n2. Ikuti seleksi (jadwal akan diumumkan)\n3. Pengumuman via email\n\nYuk aktif di UKM! Selain ilmu, kalian juga dapat networking dan pengalaman organisasi 🚀",
                'category_slug' => 'organisasi-ukm',
                'thread_type' => 'article',
            ],
        ];

        foreach ($threads as $threadData) {
            $category = $categories->where('slug', Str::slug($threadData['category_slug']))->first()
                ?? $categories->first();

            $user = $users->random();

            $thread = Thread::updateOrCreate(
                ['title' => $threadData['title']],
                [
                    'content' => $threadData['content'],
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'thread_type' => $threadData['thread_type'],
                    'status' => 'active',
                    'is_public' => true,
                ]
            );

            // Add random tags
            $randomTags = $tags->random(min(3, $tags->count()));
            $thread->tags()->syncWithoutDetaching($randomTags->pluck('id'));

            // Add some replies
            $this->addReplies($thread, $users);
        }
    }

    private function addReplies(Thread $thread, $users): void
    {
        $replyCount = rand(2, 6);

        $sampleReplies = [
            'Terima kasih sudah sharing! Sangat bermanfaat 🙏',
            'Wah, ini yang saya cari. Thanks!',
            'Mantap infonya. Bookmark dulu.',
            'Ada yang bisa bantu jelaskan lebih detail?',
            'Setuju banget dengan poin-poin di atas.',
            'Pengalaman yang inspiratif!',
            'Kapan ada event lagi? Pengen ikutan.',
            'Boleh minta kontaknya untuk tanya-tanya lebih lanjut?',
            'Up! Semoga banyak yang lihat.',
            'Nice share! Keep it up 💪',
            'Ini berguna banget buat yang lagi nyari info.',
            'Sudah coba dan memang works! Recommended.',
        ];

        for ($i = 0; $i < $replyCount; $i++) {
            Post::create([
                'thread_id' => $thread->id,
                'user_id' => $users->random()->id,
                'content' => $sampleReplies[array_rand($sampleReplies)],
                'status' => 'visible',
            ]);
        }
    }
}
