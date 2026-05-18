<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Utilities;

use Digitonic\FilamentRichEditorTools\Contracts\ProvidesRichEditorMentionProviders;
use Digitonic\FilamentRichEditorTools\Enums\RenderType;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\TextColor;
use InvalidArgumentException;

/**
 * Due to the nature of how RichContent works, whatever you do to the editor,
 * you need to do to the renderer as well. This class centralizes the renderer configuration
 */
class RichEditorUtil
{
    /**
     * @param  array<string,mixed>|string|null  $content
     */
    public static function render(array|null|string $content, RenderType $renderType = RenderType::HTML, int $maxDepth = 3): string|array|RichContentRenderer
    {
        $renderer = RichContentRenderer::make($content);
        $renderer = self::commonChainables($renderer);

        return $renderType->getRenderMethod($renderer, $maxDepth);
    }

    public static function make(string $field, string $label = 'Content'): RichEditor
    {
        $editor = RichEditor::make($field)
            ->label($label)
            ->json()
            ->columnSpan([
                'sm' => 2,
            ])
            ->floatingToolbars([
                'paragraph' => [
                    'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link', 'h1', 'h2', 'h3',
                ],
                'heading' => [
                    'h1', 'h2', 'h3',
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
                ['bold', 'italic', 'underline', 'strike', 'link', 'textColor'],
                ['alignStart', 'alignCenter', 'alignEnd'],
                ['details'],
                ['grid'],
                ['code'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                ['table', 'attachFiles', 'customBlocks', 'mergeTags'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
                ['undo', 'redo'],
            ]);

        return self::commonChainables($editor);
    }

    public static function commonChainables(RichContentRenderer|RichEditor $class): RichEditor|RichContentRenderer
    {
        $customBlocks = config('filament-rich-editor-tools.custom_blocks', []);
        $mentionProviders = self::getMentionProviders();

        if (filled($mentionProviders)) {
            $class->mentions($mentionProviders);
        }

        return $class
            ->customBlocks($customBlocks)
            ->textColors([
                'brand' => TextColor::make('Brand', '#0ea5e9'),
                'warning' => TextColor::make('Warning', '#f59e0b', darkColor: '#fbbf24'),
                ...TextColor::getDefaults(),
            ]);
    }

    /**
     * @return array<MentionProvider>
     */
    protected static function getMentionProviders(): array
    {
        $mentionProviders = [];

        foreach (config('filament-rich-editor-tools.mention_providers', []) as $provider) {
            $resolvedProvider = app($provider);

            if (! $resolvedProvider instanceof ProvidesRichEditorMentionProviders) {
                throw new InvalidArgumentException(sprintf(
                    'Configured rich editor mention provider [%s] must implement [%s].',
                    $provider,
                    ProvidesRichEditorMentionProviders::class,
                ));
            }

            array_push($mentionProviders, ...$resolvedProvider->getMentionProviders());
        }

        return $mentionProviders;
    }
}
