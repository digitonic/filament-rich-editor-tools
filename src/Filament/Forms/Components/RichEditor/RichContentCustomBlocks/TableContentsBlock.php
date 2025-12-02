<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Illuminate\Database\Eloquent\Model;

class TableContentsBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'table_contents';
    }

    public static function getLabel(): string
    {
        return 'Table contents';
    }

    public static function configureEditorAction(Action $action): Action
    {
        $parentComponentState = $action->getSchemaComponent()->getStatePath();

        return $action
            ->modalDescription('Configure the table contents block')
            ->schema([
                Hidden::make('modelId')
                    /** @phpstan-ignore-next-line */
                    ->default(fn (?Model $model) => ($model) ? $model->id : null),
                Hidden::make('modelClass')
                    ->default(fn (?Model $model) => ($model) ? get_class($model) : null),
                // Get the field name that the rich editor is stored against
                Hidden::make('modelField')
                    ->default($parentComponentState),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        /** @var view-string $view */
        $view = 'filament-rich-editor-tools::filament.rich-editor-custom-blocks.previews.table-contents';

        return view($view)
            ->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        /** @var view-string $view */
        $view = 'filament-rich-editor-tools::filament.rich-editor-custom-blocks.rendered.table-contents';

        return view($view, $config)
            ->render();
    }
}
