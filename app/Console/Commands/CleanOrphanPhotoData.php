<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Photo;

class CleanOrphanPhotoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo:clean-orphan-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan data orphan (likes, comments, downloads) untuk foto yang sudah dihapus';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembersihan data orphan...');
        
        try {
            $result = Photo::cleanOrphanData();
            
            if ($result) {
                $this->info('✅ Data orphan berhasil dibersihkan!');
                return 0;
            } else {
                $this->error('❌ Gagal membersihkan data orphan');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
