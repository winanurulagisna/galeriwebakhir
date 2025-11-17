<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Gallery;
use App\Models\Photo;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:existing-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing images from public/images to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of existing images...');

        // Create default category if not exists
        $defaultCategory = Category::firstOrCreate(
            ['name' => 'Umum'],
            ['description' => 'Kategori default untuk berita umum']
        );

        // Sample news data with existing images
        $newsData = [
            [
                'judul' => 'Upacara Peringatan Hari Kemerdekaan Indonesia',
                'isi' => 'Kegiatan upacara bendera dalam rangka memperingati Hari Kemerdekaan Republik Indonesia yang dilaksanakan di lapangan sekolah dengan penuh khidmat dan semangat nasionalisme. Kegiatan ini diikuti oleh seluruh siswa, guru, dan staf sekolah.',
                'image' => 'upacara17.JPG',
                'kategori_id' => $defaultCategory->id,
                'status' => 'published'
            ],
            [
                'judul' => 'Kegiatan TRANSFORKR4B',
                'isi' => 'Program TRANSFOKR4B untuk meningkatkan kompetensi siswa dalam bidang teknologi informasi dan komunikasi di era modern. Program ini bertujuan untuk mempersiapkan siswa menghadapi tantangan digital di masa depan.',
                'image' => 'transforkrab.JPG',
                'kategori_id' => $defaultCategory->id,
                'status' => 'published'
            ],
            [
                'judul' => 'Masa Pengenalan Lingkungan Sekolah',
                'isi' => 'MPLS tahun ajaran 2024/2025 berlangsung dengan lancar dan penuh semangat dari seluruh peserta didik baru untuk mengenal lingkungan sekolah. Kegiatan ini membantu siswa baru beradaptasi dengan lingkungan sekolah.',
                'image' => 'mpls.JPG',
                'kategori_id' => $defaultCategory->id,
                'status' => 'published'
            ],
            [
                'judul' => 'Peringatan Maulid Nabi Muhammad SAW',
                'isi' => 'Kegiatan peringatan Maulid Nabi Muhammad SAW yang diisi dengan berbagai kegiatan keagamaan dan pembacaan sholawat. Kegiatan ini bertujuan untuk meningkatkan keimanan dan ketakwaan siswa.',
                'image' => 'maulidnabi.JPG',
                'kategori_id' => $defaultCategory->id,
                'status' => 'published'
            ],
            [
                'judul' => 'Festival Adat (FEDAT)',
                'isi' => 'Festival budaya yang menampilkan berbagai kesenian dan adat istiadat dari berbagai daerah di Indonesia untuk melestarikan budaya bangsa. Festival ini menjadi ajang untuk memperkenalkan keragaman budaya Indonesia.',
                'image' => 'fedat.JPG',
                'kategori_id' => $defaultCategory->id,
                'status' => 'published'
            ],
            [
                'judul' => 'Kunjungan Industri',
                'isi' => 'Siswa kelas XII melakukan kunjungan industri untuk mengenal dunia kerja yang sesungguhnya dan mempersiapkan diri memasuki dunia profesional. Kunjungan ini memberikan pengalaman langsung tentang dunia industri.',
                'image' => 'kunjungan.JPG',
                'kategori_id' => $defaultCategory->id,
                'status' => 'published'
            ]
        ];

        // Create news posts
        foreach ($newsData as $news) {
            $post = Post::create($news);
            $this->info("Created post: {$post->judul}");
        }

        // Sample gallery data with existing images
        $galleryData = [
            [
                'title' => 'Bela Negara',
                'caption' => 'Kegiatan bela negara untuk membangun semangat patriotisme dan nasionalisme siswa',
                'image' => 'belanegara.JPG',
                'status' => 'active'
            ],
            [
                'title' => 'Kompetisi Pramuka',
                'caption' => 'Kompetisi kepramukaan tingkat kabupaten yang diikuti oleh siswa SMKN 4 KOTA BOGOR',
                'image' => 'komprapramuka.JPG',
                'status' => 'active'
            ],
            [
                'title' => 'Musyawarah Besar',
                'caption' => 'Kegiatan musyawarah besar organisasi siswa untuk membahas program kerja tahunan',
                'image' => 'mubes.JPG',
                'status' => 'active'
            ],
            [
                'title' => 'P5 Solat Duha',
                'caption' => 'Program Penguatan Profil Pelajar Pancasila melalui kegiatan solat duha berjamaah',
                'image' => 'p5solatduha.JPG',
                'status' => 'active'
            ],
            [
                'title' => 'Praktik Mapel',
                'caption' => 'Kegiatan praktik mata pelajaran untuk meningkatkan kompetensi siswa secara langsung',
                'image' => 'praktekmapil.JPG',
                'status' => 'active'
            ],
            [
                'title' => 'Upacara Rutin',
                'caption' => 'Kegiatan upacara bendera rutin setiap hari Senin untuk membangun karakter disiplin',
                'image' => 'upacararutin.JPG',
                'status' => 'active'
            ]
        ];

        // Create galleries
        foreach ($galleryData as $galleryInfo) {
            $gallery = Gallery::create([
                'post_id' => null,
                'position' => 1,
                'status' => $galleryInfo['status']
            ]);

            // Create photo for gallery
            Photo::create([
                'gallery_id' => $gallery->id,
                'file' => $galleryInfo['image'],
                'judul' => $galleryInfo['title']
            ]);

            $this->info("Created gallery: {$galleryInfo['title']}");
        }

        // Sample ekstrakurikuler data
        $ekstrakurikulerData = [
            [
                'name' => 'Paskibra',
                'description' => 'Mengembangkan jiwa kepemimpinan dan nasionalisme melalui kegiatan pengibaran bendera dan latihan baris-berbaris.',
                'image' => 'paskibeks.JPG',
                'icon' => 'fas fa-flag',
                'status' => 'aktif',
                'members' => 30,
                'color' => 'red',
                'benefits' => 'Membangun karakter disiplin, kepemimpinan, dan nasionalisme',
                'schedule' => 'Senin & Rabu, 15:00-17:00',
                'location' => 'Lapangan Sekolah',
                'instructor' => 'Pak Ahmad'
            ],
            [
                'name' => 'Pramuka',
                'description' => 'Membentuk karakter kepemimpinan, kemandirian, dan kepedulian sosial melalui kegiatan kepramukaan.',
                'image' => 'pramukaeks.JPG',
                'icon' => 'fas fa-campground',
                'status' => 'aktif',
                'members' => 45,
                'color' => 'green',
                'benefits' => 'Mengembangkan keterampilan hidup dan kepemimpinan',
                'schedule' => 'Selasa & Kamis, 15:00-17:00',
                'location' => 'Aula Sekolah',
                'instructor' => 'Bu Siti'
            ],
            [
                'name' => 'Silat',
                'description' => 'Melestarikan dan mengembangkan seni bela diri tradisional Indonesia dengan nilai-nilai budaya yang luhur.',
                'image' => 'silateks.JPG',
                'icon' => 'fas fa-fist-raised',
                'status' => 'aktif',
                'members' => 25,
                'color' => 'orange',
                'benefits' => 'Mengembangkan fisik, mental, dan spiritual',
                'schedule' => 'Senin & Jumat, 16:00-18:00',
                'location' => 'Lapangan Sekolah',
                'instructor' => 'Pak Budi'
            ],
            [
                'name' => 'Futsal',
                'description' => 'Mengembangkan kemampuan fisik, kerja tim, dan semangat sportivitas melalui olahraga futsal.',
                'image' => 'futsaleks.JPG',
                'icon' => 'fas fa-futbol',
                'status' => 'aktif',
                'members' => 40,
                'color' => 'blue',
                'benefits' => 'Mengembangkan kemampuan fisik dan kerja tim',
                'schedule' => 'Selasa & Kamis, 16:00-18:00',
                'location' => 'Lapangan Futsal',
                'instructor' => 'Pak Rudi'
            ],
            [
                'name' => 'Paduan Suara',
                'description' => 'Mengembangkan bakat musik dan harmonisasi suara melalui latihan paduan suara yang terstruktur.',
                'image' => 'paduansuaraeks.JPG',
                'icon' => 'fas fa-music',
                'status' => 'aktif',
                'members' => 35,
                'color' => 'purple',
                'benefits' => 'Mengembangkan bakat musik dan harmonisasi',
                'schedule' => 'Rabu & Jumat, 15:00-17:00',
                'location' => 'Aula Musik',
                'instructor' => 'Bu Rina'
            ],
            [
                'name' => 'PMR',
                'description' => 'Mengembangkan kemampuan pertolongan pertama dan kepedulian sosial dalam situasi darurat.',
                'image' => 'pmreks.JPG',
                'icon' => 'fas fa-heartbeat',
                'status' => 'aktif',
                'members' => 30,
                'color' => 'red',
                'benefits' => 'Mengembangkan kemampuan pertolongan pertama',
                'schedule' => 'Senin & Kamis, 15:00-17:00',
                'location' => 'Ruang PMR',
                'instructor' => 'Bu Dewi'
            ],
            [
                'name' => 'Basket',
                'description' => 'Mengembangkan kemampuan fisik, strategi tim, dan semangat kompetitif melalui olahraga basket.',
                'image' => 'basketeks.JPG',
                'icon' => 'fas fa-basketball-ball',
                'status' => 'aktif',
                'members' => 38,
                'color' => 'orange',
                'benefits' => 'Mengembangkan kemampuan fisik dan strategi tim',
                'schedule' => 'Selasa & Jumat, 16:00-18:00',
                'location' => 'Lapangan Basket',
                'instructor' => 'Pak Andi'
            ],
            [
                'name' => 'Tari',
                'description' => 'Melestarikan dan mengembangkan seni tari tradisional Indonesia dengan gerakan yang indah dan bermakna.',
                'image' => 'tarieks.JPG',
                'icon' => 'fas fa-dancing',
                'status' => 'aktif',
                'members' => 28,
                'color' => 'pink',
                'benefits' => 'Melestarikan budaya dan mengembangkan kreativitas',
                'schedule' => 'Rabu & Sabtu, 15:00-17:00',
                'location' => 'Aula Tari',
                'instructor' => 'Bu Maya'
            ],
            [
                'name' => 'Rohis',
                'description' => 'Mengembangkan pemahaman agama Islam dan membentuk karakter religius yang baik dalam kehidupan sehari-hari.',
                'image' => 'rohiseskul.JPG',
                'icon' => 'fas fa-mosque',
                'status' => 'aktif',
                'members' => 42,
                'color' => 'green',
                'benefits' => 'Mengembangkan pemahaman agama dan karakter religius',
                'schedule' => 'Senin & Rabu, 15:00-17:00',
                'location' => 'Masjid Sekolah',
                'instructor' => 'Pak Ustadz'
            ]
        ];

        // Create ekstrakurikuler as galleries
        foreach ($ekstrakurikulerData as $eksData) {
            $gallery = Gallery::create([
                'title' => $eksData['name'],
                'description' => $eksData['description'],
                'status' => 'active',
                'position' => 1
            ]);
            $this->info("Created ekstrakurikuler gallery: {$gallery->title}");
        }

        $this->info('Migration completed successfully!');
        $this->info('Created:');
        $this->info('- ' . count($newsData) . ' news posts');
        $this->info('- ' . count($galleryData) . ' galleries');
        $this->info('- ' . count($ekstrakurikulerData) . ' ekstrakurikuler');
    }
}
