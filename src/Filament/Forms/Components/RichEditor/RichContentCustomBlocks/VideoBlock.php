<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class VideoBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'video';
    }

    public static function getLabel(): string
    {
        return 'Video';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the video block')
            ->schema([
                TextInput::make('url'),
                TextInput::make('caption'),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.tiptapblocks.previews.video', [
            //
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('filament.tiptapblocks.rendered.video', $config)->render();
    }
}
