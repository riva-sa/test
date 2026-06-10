<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslateCheckCommand extends Command
{
    protected $signature = 'translate:check {--lang= : Compare specific language file group against Arabic}';

    protected $description = 'Check translation files for missing keys compared to Arabic (source of truth)';

    public function handle()
    {
        $langPath = base_path('lang');
        $arPath = $langPath . '/ar/public.php';
        $enPath = $langPath . '/en/public.php';

        if (!File::exists($arPath)) {
            $this->error('Arabic translation file not found: ' . $arPath);
            return 1;
        }

        if (!File::exists($enPath)) {
            $this->error('English translation file not found: ' . $enPath);
            return 1;
        }

        $arKeys = $this->flattenTranslations(include $arPath);
        $enKeys = $this->flattenTranslations(include $enPath);

        $missingInEn = array_diff_key($arKeys, $enKeys);
        $extraInEn = array_diff_key($enKeys, $arKeys);

        if ($this->option('lang')) {
            $group = $this->option('lang');
            $missingInEn = array_filter($missingInEn, fn($key) => str_starts_with($key, $group . '.'), ARRAY_FILTER_USE_KEY);
            $extraInEn = array_filter($extraInEn, fn($key) => str_starts_with($key, $group . '.'), ARRAY_FILTER_USE_KEY);
        }

        if (empty($missingInEn) && empty($extraInEn)) {
            $this->info('All translation keys are in sync!');
            return 0;
        }

        if (!empty($missingInEn)) {
            $this->warn('Missing in English (en):');
            foreach ($missingInEn as $key => $value) {
                $this->line("  [{$key}] => {$value}");
            }
        }

        if (!empty($extraInEn)) {
            $this->warn('Extra in English (en) - not in Arabic source:');
            foreach ($extraInEn as $key => $value) {
                $this->line("  [{$key}] => {$value}");
            }
        }

        return 0;
    }

    private function flattenTranslations(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenTranslations($value, $fullKey));
            } else {
                $result[$fullKey] = $value;
            }
        }
        return $result;
    }
}
