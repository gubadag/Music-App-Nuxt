<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class MusicController extends Controller
{



    public function uploadFromUrls(Request $request): JsonResponse
    {
        $data = $request->validate([
            'artist' => 'required|string',
            'audios' => 'required|array',
            'audios.*.title' => 'required|string',
            'audios.*.src' => 'required|url',
            'audios.*.author' => 'required|string',
            'audios.*.duration' => 'required|numeric',
            'audios.*.durationText' => 'required|string',
            'audios.*.playCount' => 'required|numeric',
        ]);

        $artist = Str::slug($data['artist']); // e.g. "A.Robi" => "a-robi"
        $audios = $data['audios'];
        $updatedAudios = [];

        foreach ($audios as $audio) {
            try {
                $content = @file_get_contents($audio['src']);
                if ($content === false || strlen($content) === 0) {
                    Log::warning("Failed to download audio: {$audio['src']} (no content returned)");
                    continue;
                }

                $filename = Str::slug($audio['title']) . '.mp3';
                $path = "audios/{$artist}/{$filename}";

                Storage::disk('r2')->put($path, $content);

                $audio['src'] = Storage::disk('r2')->url($path);
                $updatedAudios[] = $audio;

            } catch (\Exception $e) {
                Log::warning("Failed to download audio: {$audio['src']}. Error: {$e->getMessage()}");
                continue;
            }
        }

        // Generate JS file content
        $jsContent = "export const artist = \"" . addslashes($data['artist']) . "\";\n";
        $jsContent .= "export const audios = " . json_encode($updatedAudios, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ";\n";

        // Define frontend data folder path
        $frontendDataPath = base_path('music/src/data');

        // Create directory if not exists
        if (!file_exists($frontendDataPath)) {
            mkdir($frontendDataPath, 0755, true);
        }

        // Create unique filename for new JS file
        $uniqueSuffix = date('Ymd_His');
        $filename = "audios{$artist}_{$uniqueSuffix}.js";

        $fullPath = $frontendDataPath . DIRECTORY_SEPARATOR . $filename;

        try {
            file_put_contents($fullPath, $jsContent);
        } catch (\Exception $e) {
            Log::error("Failed to write JS file: {$fullPath}. Error: {$e->getMessage()}");
            return response()->json([
                'message' => 'Failed to write JS file.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Audios uploaded successfully.',
            'artist' => $artist,
            'file' => $filename,
        ]);
    }

}
