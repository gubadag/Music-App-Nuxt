<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadLocalImages extends Command
{
    protected $signature = 'upload:local-images';
    protected $description = 'Upload image files from local folder to Cloudflare R2';

    public function handle()
    {
        $localDirectory = 'D:/Downloads from telegram/sss/images';

        // Normalize path slashes (for Windows)
        $localDirectory = str_replace('\\', '/', $localDirectory);

        // Define allowed image extensions
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Use glob pattern to find all image files with the above extensions
        $imageFiles = [];
        foreach ($imageExtensions as $ext) {
            $imageFiles = array_merge($imageFiles, glob($localDirectory . '/*.' . $ext));
        }

        if (empty($imageFiles)) {
            $this->error('❌ No image files found in: ' . $localDirectory);
            return;
        }

        // Connect to Cloudflare R2 disk
        $r2 = Storage::disk('r2');

        $this->info("📤 Uploading " . count($imageFiles) . " image files to R2...");

        foreach ($imageFiles as $filePath) {
            $filename = basename($filePath);
            $this->info("Uploading: $filename");

            // Upload to "images/" folder in R2
            $r2->put('images/' . $filename, file_get_contents($filePath));

            $this->info("✅ Uploaded: images/$filename");
        }

        $this->info("🎉 All image files have been uploaded to R2.");
    }
}
