<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Photo;
use App\Helpers\ImageHelper;

class ConvertHeicPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo:convert-heic {--force : Force conversion even if JPEG version exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Konversi foto HEIC yang sudah ada ke format JPEG untuk kompatibilitas browser';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai konversi foto HEIC...');
        
        // Cari semua foto dengan format HEIC/HEIF
        $heicPhotos = Photo::where(function($query) {
            $query->where('file', 'like', '%.heic')
                  ->orWhere('file', 'like', '%.heif')
                  ->orWhere('file', 'like', '%.HEIC')
                  ->orWhere('file', 'like', '%.HEIF');
        })->get();
        
        if ($heicPhotos->isEmpty()) {
            $this->info('Tidak ada foto HEIC yang ditemukan.');
            return 0;
        }
        
        $this->info("Ditemukan {$heicPhotos->count()} foto HEIC untuk dikonversi.");
        
        $converted = 0;
        $skipped = 0;
        $failed = 0;
        
        $progressBar = $this->output->createProgressBar($heicPhotos->count());
        $progressBar->start();
        
        foreach ($heicPhotos as $photo) {
            try {
                $originalPath = public_path($photo->file);
                
                if (!file_exists($originalPath)) {
                    $this->newLine();
                    $this->warn("File tidak ditemukan: {$photo->file}");
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Cek apakah sudah ada versi JPEG
                $jpegPath = preg_replace('/\.(heic|heif)$/i', '.jpg', $originalPath);
                if (file_exists($jpegPath) && !$this->option('force')) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Konversi HEIC ke JPEG
                if (extension_loaded('imagick')) {
                    try {
                        $imagick = new \Imagick($originalPath);
                        $imagick->setImageFormat('jpeg');
                        $imagick->setImageCompressionQuality(85);
                        $imagick->writeImage($jpegPath);
                        $imagick->clear();
                        $imagick->destroy();
                        
                        // Update database dengan path JPEG baru
                        $newWebPath = preg_replace('/\.(heic|heif)$/i', '.jpg', $photo->file);
                        $photo->update(['file' => $newWebPath]);
                        
                        // Hapus file HEIC asli untuk menghemat space
                        if (file_exists($originalPath)) {
                            unlink($originalPath);
                        }
                        
                        $converted++;
                        
                    } catch (\Exception $e) {
                        $this->newLine();
                        $this->error("Gagal konversi {$photo->file}: " . $e->getMessage());
                        $failed++;
                    }
                } else {
                    $this->newLine();
                    $this->error('ImageMagick tidak tersedia untuk konversi HEIC');
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error processing {$photo->file}: " . $e->getMessage());
                $failed++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Konversi selesai!");
        $this->info("✅ Berhasil dikonversi: {$converted}");
        $this->info("⏭️  Dilewati: {$skipped}");
        $this->info("❌ Gagal: {$failed}");
        
        return 0;
    }
}
