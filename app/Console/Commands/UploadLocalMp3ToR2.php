<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadLocalMp3ToR2 extends Command
{
    protected $signature = 'upload:local-mp3';
    protected $description = 'Upload MP3 files from local folder to Cloudflare R2';

    public function handle()
    {
        $localDirectory = 'D:\Downloads from telegram\sss';

        // Normalize path slashes for Windows
        $localDirectory = str_replace('\\', '/', $localDirectory);

        // Get all MP3 files
        $mp3Files = glob($localDirectory . '/*.mp3');

        if (empty($mp3Files)) {
            $this->error('❌ No MP3 files found in: ' . $localDirectory);
            return;
        }

        // Connect to R2
        $r2 = Storage::disk('r2');

        $this->info("📤 Uploading " . count($mp3Files) . " MP3 files to R2...");

        foreach ($mp3Files as $filePath) {
            $filename = basename($filePath);
            $this->info("Uploading: $filename");

            // ✅ Upload to "songs/" folder in R2
            $r2->put('songs/' . $filename, file_get_contents($filePath));

            $this->info("✅ Uploaded: songs/$filename");
        }

        $this->info("🎉 All MP3 files have been uploaded to R2.");
    }
}
