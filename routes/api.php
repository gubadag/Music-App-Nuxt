<?php

use App\Http\Controllers\MusicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/upload-audios', [MusicController::class, 'uploadFromUrls']);




Route::get('/r2-test', function () {
    try {
        // Try to list files in the bucket
        $files = Storage::disk('r2')->files();

        return response()->json([
            'success' => true,
            'message' => 'Connected to R2!',
            'files' => $files,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to connect to R2',
            'error' => $e->getMessage(),
        ]);
    }
});
