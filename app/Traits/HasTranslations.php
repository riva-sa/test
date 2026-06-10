<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Locale-aware attribute resolution for visitor-facing content.
 *
 * A model using this trait declares a `$translatable` array of column names.
 * For each one, a sibling `{column}_en` column is expected in the table
 * (added additively — the original column stays the Arabic source of truth).
 *
 * When the active locale is English AND the `_en` value is filled, reading the
 * base attribute (e.g. `$project->name`) transparently returns the English
 * value. Otherwise it falls back to the original Arabic value. This means the
 * existing frontend Blade views need no changes — `$project->name` keeps
 * working and simply localizes itself.
 *
 * To make this work even when a query selects an explicit column subset (e.g.
 * `->select(['id', 'name'])` or an eager-load constraint like
 * `with('developer:id,name')`), a `beforeQuery` hook automatically appends the
 * matching `_en` column whenever a translatable base column is selected. This
 * keeps every page consistent without hand-editing each query.
 *
 * Admin/CRM runs under the default `ar` locale (it is not inside the `/en`
 * route prefix group), so editing in Filament always sees the raw Arabic
 * source column — the override never interferes with administration.
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::addGlobalScope('hasTranslations', function (Builder $builder) {
            $model = $builder->getModel();
            $translatable = method_exists($model, 'getTranslatableAttributes')
                ? $model->getTranslatableAttributes()
                : [];

            if (empty($translatable)) {
                return;
            }

            $builder->getQuery()->beforeQuery(function (QueryBuilder $query) use ($translatable) {
                // Only act on explicit column selections. An empty/`*` selection
                // already pulls the `_en` columns, and aggregate/expression
                // selections (count, etc.) are left untouched.
                if (empty($query->columns)) {
                    return;
                }

                $additions = [];

                foreach ($query->columns as $column) {
                    if (! is_string($column)) {
                        continue;
                    }

                    $dot = strrpos($column, '.');
                    $prefix = $dot === false ? '' : substr($column, 0, $dot + 1);
                    $name = $dot === false ? $column : substr($column, $dot + 1);

                    if (! in_array($name, $translatable, true)) {
                        continue;
                    }

                    $enColumn = $prefix.$name.'_en';

                    if (! in_array($enColumn, $query->columns, true) && ! in_array($enColumn, $additions, true)) {
                        $additions[] = $enColumn;
                    }
                }

                if ($additions) {
                    $query->columns = array_merge($query->columns, $additions);
                }
            });
        });
    }

    public function getAttribute($key)
    {
        if (
            app()->getLocale() === 'en'
            && is_string($key)
            && in_array($key, $this->getTranslatableAttributes(), true)
        ) {
            // Read only from already-loaded attributes. If the `_en` column was
            // not selected by the query (e.g. partial select), fall through to
            // the base column instead of triggering MissingAttributeException
            // under strict mode (preventAccessingMissingAttributes).
            $enKey = $key.'_en';

            if (array_key_exists($enKey, $this->attributes) && filled($this->attributes[$enKey])) {
                return $this->attributes[$enKey];
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Localize serialized output too, so `toArray()` / `toJson()` / `@json($model)`
     * (e.g. project data handed to the map's JavaScript) reflect the English
     * value when the locale is English and a translation exists. Falls back to
     * the Arabic source otherwise.
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if (app()->getLocale() === 'en') {
            foreach ($this->getTranslatableAttributes() as $attr) {
                $enKey = $attr.'_en';

                if (array_key_exists($enKey, $attributes) && filled($attributes[$enKey])) {
                    $attributes[$attr] = $attributes[$enKey];
                }
            }
        }

        return $attributes;
    }

    /**
     * @return array<int, string>
     */
    public function getTranslatableAttributes(): array
    {
        return $this->translatable ?? [];
    }
}
