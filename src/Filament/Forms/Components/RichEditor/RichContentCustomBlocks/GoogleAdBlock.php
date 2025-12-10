<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class GoogleAdBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'google_ad';
    }

    public static function getLabel(): string
    {
        return 'Google ad';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the google ad block')
            ->schema([
                //
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        /** @var view-string $view */
        $view = 'filament-rich-editor-tools::filament.rich-editor-custom-blocks.previews.google-ad';

        return view($view, $config)
            ->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        /** @var view-string $view */
        $view = 'filament-rich-editor-tools::filament.rich-editor-custom-blocks.rendered.google-ad';

        return view($view, $config)
            ->render();
    }
}
