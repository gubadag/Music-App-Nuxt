<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});


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
