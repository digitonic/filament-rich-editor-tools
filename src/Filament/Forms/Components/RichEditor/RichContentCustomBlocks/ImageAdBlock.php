<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class ImageAdBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'image_ad';
    }

    public static function getLabel(): string
    {
        return 'Image ad';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the image ad block')
            ->schema([
                TextInput::make('url')
                    ->helperText('The URL the ad should link to'),
                TextInput::make('unique_identifier')
                    ->helperText('The unique identifier for the ad within GA4'),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        /** @var view-string $view */
        $view = 'filament.tiptapblocks.previews.image-ad';

        return view($view, $config)
            ->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        /** @var view-string $view */
        $view = 'filament.tiptapblocks.rendered.image-ad';

        return view($view, $config)->render();
    }
}
