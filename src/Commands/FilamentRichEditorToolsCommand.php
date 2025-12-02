<?php

namespace Digitonic\FilamentRichEditorTools\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class FilamentRichEditorToolsCommand extends Command
{
    protected $signature = 'filament-rich-editor-tools:migrate-blocks {model : Fully qualified model class, e.g. App\\Models\\Article} {column : Column containing rich editor blocks} {--dry : Run without saving changes}';

    protected $description = 'Migrate rich editor blocks JSON from tiptapBlock to customBlock';

    public function handle(): int
    {
        // Validate model class
        $modelClass = (string) $this->argument('model');
        $column = (string) $this->argument('column');
        $dryRun = (bool) $this->option('dry');

        if (! class_exists($modelClass)) {
            $this->error("Model class not found: {$modelClass}");

            return self::INVALID;
        }

        /** @var class-string<Model> $modelClass */
        if (! is_subclass_of($modelClass, Model::class)) {
            $this->error("Provided class is not an Eloquent Model: {$modelClass}");

            return self::INVALID;
        }

        // Instantiate model and ensure column exists
        /** @var Model $model */
        $model = new $modelClass();
        $table = $model->getTable();

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
            ->chunkById(200, function ($records) use (&$updated, &$skipped, $column, $dryRun): void {
                /** @var Model $record */
                foreach ($records as $record) {
                    $raw = $record->{$column};

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
                    $migratedJson = json_encode($migrated);

                    if ($original === $migratedJson) {
                        $skipped++;
                        continue;
                    }

                    if ($dryRun) {
                        $updated++;
                        continue;
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
     * @param array<string,mixed> $data
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
                $attrs =& $node['attrs'];
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

                if(isset($attrs['data'])) {
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
}
