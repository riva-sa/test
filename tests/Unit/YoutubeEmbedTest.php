<?php

use App\Models\ProjectMedia;

it('extracts video ID from standard YouTube URL', function () {
    $media = new ProjectMedia(['youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);
    expect($media->youtube_embed_url)->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
});

it('extracts video ID from shortened YouTube URL', function () {
    $media = new ProjectMedia(['youtube_url' => 'https://youtu.be/dQw4w9WgXcQ']);
    expect($media->youtube_embed_url)->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
});

it('extracts video ID from embed YouTube URL', function () {
    $media = new ProjectMedia(['youtube_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ']);
    expect($media->youtube_embed_url)->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
});

it('extracts video ID from shorts YouTube URL', function () {
    $media = new ProjectMedia(['youtube_url' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ']);
    expect($media->youtube_embed_url)->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
});

it('returns null for invalid YouTube URLs', function () {
    $media = new ProjectMedia(['youtube_url' => 'https://example.com/not-youtube']);
    expect($media->youtube_embed_url)->toBeNull();
});

it('returns null for empty YouTube URL', function () {
    $media = new ProjectMedia(['youtube_url' => '']);
    expect($media->youtube_embed_url)->toBeNull();
});

it('returns null for null YouTube URL', function () {
    $media = new ProjectMedia(['youtube_url' => null]);
    expect($media->youtube_embed_url)->toBeNull();
});

it('handles YouTube URL with extra query parameters', function () {
    $media = new ProjectMedia(['youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=PLrAXtmErZgOeiKm4sgNOknGvNjby9efdf']);
    expect($media->youtube_embed_url)->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
});
