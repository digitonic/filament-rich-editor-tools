<?php

namespace Digitonic\FilamentRichEditorTools\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FilamentRichEditorToolsCommand extends Command
{
    protected $signature = 'filament-rich-editor-tools:migrate-blocks {model : Fully qualified model class, e.g. App\\Models\\Article} {column : Column containing rich editor blocks} {--dry : Run without saving changes}';

    protected $description = 'Migrate rich editor content from Filament 3 (TiptapEditor) to Filament 4 (RichEditor) format';

    public function handle(): int
    {
        // Validate model class
        $modelClass = (string) $this->argument('model');
        $column = (string) $this->argument('column');
        $dryRun = (bool) $this->option('dry');
        $dotNotation = false;

        if (! class_exists($modelClass)) {
            $this->error("Model class not found: {$modelClass}");

            return self::INVALID;
        }

        // Instantiate model and ensure column exists
        /** @var Model $model */
        $model = new $modelClass;
        $table = $model->getTable();

        // There is a chance that the column provided will be in dot notation to access JSON fields.
        // We need to extract the base column name for the query.
        if (Str::contains($column, '.')) {
            $dotNotation = true;
            $column = Str::before($column, '.');
        }

        if (! \Schema::hasColumn($table, $column)) {
            $this->error("Column '{$column}' does not exist on table '{$table}'.");

            return self::INVALID;
        }

        $query = $modelClass::query()->whereNotNull($column);
        $count = (int) $query->count();
        if ($count === 0) {
            $this->info('No records found with data to migrate.');

            return self::SUCCESS;
        }

        $this->info("Found {$count} record(s) to inspect on {$modelClass}::{$column}.");

        $updated = 0;
        $skipped = 0;

        $query->orderBy($model->getKeyName())
            ->chunkById(200, function ($records) use (&$updated, &$skipped, $column, $dryRun, $dotNotation): void {
                /** @var Model $record */
                foreach ($records as $record) {
                    // If dot notation was used, we need to extract the JSON field from the column
                    if ($dotNotation) {
                        $raw = data_get($record->{$column}, Str::after($this->argument('column'), '.'));
                    } else {
                        $raw = $record->{$column};
                    }

                    if ($raw === null) {
                        $skipped++;

                        continue;
                    }

                    // Accept array or JSON string from DB
                    $data = is_string($raw) ? json_decode($raw, true) : $raw;
                    if (! is_array($data)) {
                        $skipped++;

                        continue;
                    }

                    $original = json_encode($data);
                    $migrated = $this->migrateBlocks($data);
                    $migrated = $this->migrateMarks($migrated);
                    $migratedJson = json_encode($migrated);

                    if ($original === $migratedJson) {
                        $skipped++;

                        continue;
                    }

                    if ($dryRun) {
                        $updated++;

                        continue;
                    }

                    // If dot notation was used, we need to set the JSON field back into the column
                    if ($dotNotation) {
                        $fullData = $record->{$column} ?? [];
                        data_set($fullData, Str::after($this->argument('column'), '.'), $migrated);
                        $migrated = $fullData;
                    }

                    $record->{$column} = $migrated;
                    $record->save();
                    $updated++;
                }
            });

        if ($dryRun) {
            $this->comment("Dry run complete. Would update {$updated} record(s); {$skipped} skipped.");
        } else {
            $this->comment("Migration complete. Updated {$updated} record(s); {$skipped} skipped.");
        }

        return self::SUCCESS;
    }

    /**
     * Migrate tiptapBlock -> customBlock recursively within the rich content JSON.
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    protected function migrateBlocks(array $data): array
    {
        // 1) node.type: tiptapBlock -> customBlock
        // 2) node.attrs.type -> node.attrs.id (While removing block from the type)
        $walker = function (&$node) use (&$walker): void {
            if (! is_array($node)) {
                return;
            }

            // 1) Replace node type
            if (isset($node['type']) && $node['type'] === 'tiptapBlock') {
                $node['type'] = 'customBlock';
            }

            // 2) Move attrs.type -> attrs.id (without clobbering id)
            if (isset($node['attrs']) && is_array($node['attrs'])) {
                $attrs = &$node['attrs'];
                if (array_key_exists('type', $attrs)) {
                    if (! array_key_exists('id', $attrs) || $attrs['id'] === null || $attrs['id'] === '') {
                        $attrs['id'] = $attrs['type'];
                    }

                    unset($attrs['type']);
                }

                // 3) Normalize id (only if we just moved it or it ends with Block)
                if (isset($attrs['id']) && is_string($attrs['id'])) {
                    $id = Str::lower($attrs['id']);
                    $id = trim(Str::replace('block', '', $id));
                    $attrs['id'] = $id;
                }

                if (isset($attrs['data'])) {
                    $attrs['config'] = $attrs['data'];
                    unset($attrs['data']);
                }
            }

            // Recurse into child arrays
            foreach ($node as &$child) {
                if (is_array($child)) {
                    $walker($child);
                }
            }
        };

        $copy = $data;
        $walker($copy);

        return $copy;
    }

    /**
     * Migrate textStyle marks -> textColor marks recursively within the rich content JSON.
     *
     * Transforms:
     * - mark type: textStyle -> textColor
     * - attrs.color (hex) -> attrs.data-color (color name)
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    protected function migrateMarks(array $data): array
    {
        $colorMap = $this->getColorMap();

        $walker = function (&$node) use (&$walker, $colorMap): void {
            if (! is_array($node)) {
                return;
            }

            // Transform textStyle marks to textColor marks
            if (isset($node['marks']) && is_array($node['marks'])) {
                foreach ($node['marks'] as &$mark) {
                    if (isset($mark['type']) && $mark['type'] === 'textStyle') {
                        $mark['type'] = 'textColor';

                        // Convert color hex to data-color name
                        if (isset($mark['attrs']['color'])) {
                            $hexColor = strtolower($mark['attrs']['color']);
                            $colorName = $colorMap[$hexColor] ?? $this->findClosestColor($hexColor, $colorMap);

                            $mark['attrs'] = ['data-color' => $colorName];
                        }
                    }
                }
            }

            // Recurse into child arrays (content, etc.)
            foreach ($node as &$child) {
                if (is_array($child)) {
                    $walker($child);
                }
            }
        };

        $copy = $data;
        $walker($copy);

        return $copy;
    }

    /**
     * Get the color mapping from hex values to Filament color names.
     *
     * This includes common Tailwind color hex values mapped to their names.
     * Can be extended via config('filament-rich-editor-tools.color_map')
     *
     * @return array<string, string>
     */
    protected function getColorMap(): array
    {
        // Default Tailwind CSS color mappings (shade 500/600 which are commonly used)
        $defaultMap = [
            // Reds
            '#ef4444' => 'red',
            '#dc2626' => 'red',
            '#b91c1c' => 'red',
            '#f87171' => 'red',

            // Oranges
            '#f97316' => 'orange',
            '#ea580c' => 'orange',
            '#fb923c' => 'orange',

            // Ambers
            '#f59e0b' => 'amber',
            '#d97706' => 'amber',
            '#fbbf24' => 'amber',

            // Yellows
            '#eab308' => 'yellow',
            '#ca8a04' => 'yellow',
            '#facc15' => 'yellow',

            // Limes
            '#84cc16' => 'lime',
            '#65a30d' => 'lime',
            '#a3e635' => 'lime',

            // Greens
            '#22c55e' => 'green',
            '#16a34a' => 'green',
            '#4ade80' => 'green',
            '#1e7f75' => 'green',

            // Emeralds
            '#10b981' => 'emerald',
            '#059669' => 'emerald',
            '#34d399' => 'emerald',

            // Teals
            '#14b8a6' => 'teal',
            '#0d9488' => 'teal',
            '#2dd4bf' => 'teal',

            // Cyans
            '#06b6d4' => 'cyan',
            '#0891b2' => 'cyan',
            '#22d3ee' => 'cyan',

            // Skys
            '#0ea5e9' => 'sky',
            '#0284c7' => 'sky',
            '#38bdf8' => 'sky',

            // Blues
            '#3b82f6' => 'blue',
            '#2563eb' => 'blue',
            '#60a5fa' => 'blue',

            // Indigos
            '#6366f1' => 'indigo',
            '#4f46e5' => 'indigo',
            '#818cf8' => 'indigo',

            // Violets
            '#8b5cf6' => 'violet',
            '#7c3aed' => 'violet',
            '#a78bfa' => 'violet',

            // Purples
            '#a855f7' => 'purple',
            '#9333ea' => 'purple',
            '#c084fc' => 'purple',

            // Fuchsias
            '#d946ef' => 'fuchsia',
            '#c026d3' => 'fuchsia',
            '#e879f9' => 'fuchsia',

            // Pinks
            '#ec4899' => 'pink',
            '#db2777' => 'pink',
            '#f472b6' => 'pink',

            // Roses
            '#f43f5e' => 'rose',
            '#e11d48' => 'rose',
            '#fb7185' => 'rose',

            // Grays
            '#6b7280' => 'gray',
            '#4b5563' => 'gray',
            '#9ca3af' => 'gray',
            '#374151' => 'gray',
        ];

        // Merge with any custom mappings from config
        $customMap = config('filament-rich-editor-tools.color_map', []);

        return array_merge($defaultMap, $customMap);
    }

    /**
     * Find the closest color name for a hex value not in the map.
     *
     * Falls back to 'gray' if no close match is found.
     *
     * @param  array<string, string>  $colorMap
     */
    protected function findClosestColor(string $hexColor, array $colorMap): string
    {
        $rgb = $this->hexToRgb($hexColor);
        if ($rgb === null) {
            return 'gray';
        }

        $closestColor = 'gray';
        $closestDistance = PHP_INT_MAX;

        foreach ($colorMap as $hex => $name) {
            $compareRgb = $this->hexToRgb($hex);
            if ($compareRgb === null) {
                continue;
            }

            // Calculate color distance using simple Euclidean distance in RGB space
            $distance = sqrt(
                pow($rgb['r'] - $compareRgb['r'], 2) +
                pow($rgb['g'] - $compareRgb['g'], 2) +
                pow($rgb['b'] - $compareRgb['b'], 2)
            );

            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestColor = $name;
            }
        }

        return $closestColor;
    }

    /**
     * Convert a hex color to RGB values.
     *
     * @return array{r: int, g: int, b: int}|null
     */
    protected function hexToRgb(string $hex): ?array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6) {
            return null;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }
}
