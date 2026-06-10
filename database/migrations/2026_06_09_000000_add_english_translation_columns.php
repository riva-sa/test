<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive English translation columns for visitor-facing content.
 *
 * SAFETY: This migration is purely additive. It only ADDS new nullable
 * `*_en` columns and never touches, transforms, or drops any existing
 * (Arabic) source column. The Arabic columns remain the source of truth.
 * Rollback simply drops the added columns — no data loss is possible on
 * the original content.
 */
return new class extends Migration
{
    /**
     * Map of table => translatable columns that need an `_en` sibling.
     */
    private array $map = [
        'projects' => ['name', 'description', 'address', 'bulding_style'],
        'units' => ['title', 'unit_type', 'description'],
        'developers' => ['name'],
        'project_types' => ['name'],
        'cities' => ['name'],
        'states' => ['name'],
        'features' => ['name', 'description'],
        'guarantees' => ['name', 'description'],
        'landmarks' => ['name', 'description'],
        'partners' => ['name'],
        'content_blocks' => ['content'],
    ];

    public function up(): void
    {
        foreach ($this->map as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns) {
                foreach ($columns as $column) {
                    $enColumn = $column.'_en';

                    // Never overwrite an existing column.
                    if (Schema::hasColumn($table, $enColumn) || ! Schema::hasColumn($table, $column)) {
                        continue;
                    }

                    // Long-form fields use text, short labels use string. Mirror
                    // the source intent: description/content/body are long.
                    if (in_array($column, ['description', 'content', 'body'], true)) {
                        $blueprint->text($enColumn)->nullable()->after($column);
                    } else {
                        $blueprint->string($enColumn)->nullable()->after($column);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->map as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns) {
                foreach ($columns as $column) {
                    $enColumn = $column.'_en';
                    if (Schema::hasColumn($table, $enColumn)) {
                        $blueprint->dropColumn($enColumn);
                    }
                }
            });
        }
    }
};
