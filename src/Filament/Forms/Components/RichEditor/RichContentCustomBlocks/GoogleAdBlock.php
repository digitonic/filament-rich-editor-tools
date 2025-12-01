<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class GoogleAdBlock extends RichContentCustomBlock
{
    //     public string $preview = 'filament.tiptapblocks.previews.google-ad';
    //
    //    public string $rendered = 'filament.tiptapblocks.rendered.google-ad';
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
        return view('filament.tiptapblocks.previews.google-ad', [
            //
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('filament.tiptapblocks.rendered.google-ad', $config)->render();
    }
}
