<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use App\Models\Article;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

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
        return $action
            ->modalDescription('Configure the table contents block')
            ->schema([
                Hidden::make('articleId')
                    ->default(fn (Article $article) => $article->id),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament-rich-editor-tools::filament.rich-editor-custom-blocks.previews.table-contents')
            ->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('filament-rich-editor-tools::filament.rich-editor-custom-blocks.rendered.table-contents', $config)
            ->render();
    }
}
