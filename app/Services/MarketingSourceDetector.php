<?php

namespace App\Services;

class MarketingSourceDetector
{
    protected array $platformPatterns = [
        'snapchat' => ['snap', 'sc'],
        'instagram' => ['instagram', 'ig'],
        'tiktok' => ['tiktok', 'ticktok'],
        'facebook' => ['facebook', 'fb'],
        'twitter' => ['twitter', 'x.com'],
        'linkedin' => ['linkedin'],
        'whatsapp' => ['whatsapp', 'wa.me'],
        'google' => ['google'],
        'youtube' => ['youtube', 'youtu.be'],
    ];

    public function detect(string $source = null, string $userAgent = null, string $referer = null): string
    {
        $input = strtolower(trim($source ?? ''));

        if (!empty($input)) {
            foreach ($this->platformPatterns as $platform => $patterns) {
                foreach ($patterns as $pattern) {
                    if (str_contains($input, $pattern)) {
                        return ucfirst($platform);
                    }
                }
            }

            return $source;
        }

        $detectionSource = strtolower(trim($userAgent ?? '') . ' ' . trim($referer ?? ''));

        foreach ($this->platformPatterns as $platform => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($detectionSource, $pattern)) {
                    return ucfirst($platform);
                }
            }
        }

        return $source ?? 'Direct';
    }

    public static function make(): self
    {
        return new self();
    }
}