<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $messages = [
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'ahmad.wijaya@email.com',
                'message' => 'Sangat senang dengan program ekstrakurikuler yang ditawarkan sekolah ini. Anak saya menjadi lebih aktif dan kreatif.',
                'status' => 'read'
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@email.com',
                'message' => 'Fasilitas sekolah sangat memadai dan guru-gurunya sangat ramah. Proses pembelajaran berjalan dengan baik.',
                'status' => 'read'
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'message' => 'Anak saya sangat senang belajar di SMKN 4 KOTA BOGOR. Prestasi akademiknya meningkat drastis.',
                'status' => 'unread'
            ],
            [
                'name' => 'Rina Sari',
                'email' => 'rina.sari@email.com',
                'message' => 'Program pembelajaran yang sangat menarik dan mudah dipahami. Siswa menjadi lebih antusias belajar.',
                'status' => 'read'
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi.kurniawan@email.com',
                'message' => 'Sekolah ini memberikan pendidikan yang berkualitas tinggi. Sangat direkomendasikan untuk orang tua.',
                'status' => 'unread'
            ],
            [
                'name' => 'Maya Putri',
                'email' => 'maya.putri@email.com',
                'message' => 'Lingkungan sekolah yang kondusif dan mendukung perkembangan siswa. Terima kasih SMKN 4!',
                'status' => 'read'
            ],
            [
                'name' => 'Agus Prasetyo',
                'email' => 'agus.prasetyo@email.com',
                'message' => 'Sistem pembelajaran yang modern dan inovatif. Siswa diajarkan untuk berpikir kritis dan kreatif.',
                'status' => 'unread'
            ],
            [
                'name' => 'Lina Marlina',
                'email' => 'lina.marlina@email.com',
                'message' => 'Kegiatan ekstrakurikuler yang beragam dan menarik. Anak saya sangat menikmati setiap kegiatan.',
                'status' => 'read'
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono@email.com',
                'message' => 'Prestasi sekolah yang membanggakan. Siswa-siswa berprestasi di berbagai kompetisi.',
                'status' => 'unread'
            ],
            [
                'name' => 'Sari Indah',
                'email' => 'sari.indah@email.com',
                'message' => 'Komunikasi antara sekolah dan orang tua sangat baik. Informasi selalu update dan jelas.',
                'status' => 'read'
            ]
        ];

        foreach ($messages as $message) {
            Message::create($message);
        }
    }
}