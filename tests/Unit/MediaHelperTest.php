<?php

use App\Helpers\MediaHelper;
use App\Models\OptimizedImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns placeholder when path is empty', function () {
    expect(MediaHelper::getUrl(''))->toBe('https://placehold.co/800x600?text=riva.sa');
    expect(MediaHelper::getUrl(null))->toBe('https://placehold.co/800x600?text=riva.sa');
});

it('returns fallback url when optimization service is unavailable or no variants exist', function () {
    Storage::fake('public');
    
    // Fallback URL assumes no variants
    $url = MediaHelper::getUrl('some/path.jpg');
    expect($url)->toBe(Storage::disk('public')->url('some/path.jpg'));
});

it('returns optimized variant url when exists', function () {
    Storage::fake('public');
    Storage::disk('public')->put('optimized/some/path-thumbnail.webp', 'fake-image-content');

    OptimizedImage::create([
        'original_path' => 'some/path.jpg',
        'variant_type' => 'thumbnail',
        'variant_path' => 'optimized/some/path-thumbnail.webp',
        'format' => 'webp',
        'status' => 'completed',
    ]);

    $url = MediaHelper::getUrl('some/path.jpg', 'thumbnail');
    expect($url)->toBe(Storage::disk('public')->url('optimized/some/path-thumbnail.webp'));
});
