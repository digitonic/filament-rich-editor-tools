<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Utilities;

use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ButtonBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\TableContentsBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\VideoBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\GoogleAdBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ImageAdBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ProsAndConsBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\TwitterEmbedBlock;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\TextColor;

/**
 * Due to the nature of how RichContent works, whatever you do to the editor,
 * you need to do to the renderer as well. This class centralizes the renderer configuration
 */
class RichEditorUtil
{
    /**
     * @param  array<string,mixed>|string|null  $content
     */
    public static function render(array|null|string $content): string
    {
        $renderer = RichContentRenderer::make($content);

        return self::commonChainables($renderer)->toUnsafeHtml();
    }

    public static function make(string $field, string $label = 'Content'): RichEditor
    {
        $editor = RichEditor::make($field)
            ->label($label)
            ->json()
            ->required()
            ->columnSpan([
                'sm' => 2,
            ])
            ->floatingToolbars([
                'paragraph' => [
                    'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link',
                ],
                'heading' => [
                    'h2', 'h3',
                ],
                'grid' => [
                    'gridDelete',
                ],
                'table' => [
                    'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                    'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                    'tableMergeCells', 'tableSplitCell',
                    'tableToggleHeaderRow',
                    'tableDelete',
                ],
            ])
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link', 'textColor'],
                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                ['details'],
                ['grid'],
                ['code'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                ['table', 'attachFiles', 'customBlocks', 'mergeTags'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
                ['undo', 'redo'],
            ]);

        /** @phpstan-ignore-next-line  */
        return self::commonChainables($editor);
    }

    public static function commonChainables(RichContentRenderer|RichEditor $class): RichEditor|RichContentRenderer
    {
        $customBlocks = array_merge(config('filament-rich-editor-tools.custom_blocks', []), [
            ButtonBlock::class,
            TwitterEmbedBlock::class,
            GoogleAdBlock::class,
            ImageAdBlock::class,
            VideoBlock::class,
            ProsAndConsBlock::class,
            TableContentsBlock::class
        ]);

        return $class
            ->customBlocks($customBlocks)
            ->textColors([
                'brand' => TextColor::make('Brand', '#0ea5e9'),
                'warning' => TextColor::make('Warning', '#f59e0b', darkColor: '#fbbf24'),
                ...TextColor::getDefaults(),
            ]);
    }
}
