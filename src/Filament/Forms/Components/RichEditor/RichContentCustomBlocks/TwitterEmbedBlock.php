<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class TwitterEmbedBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'twitter_embed';
    }

    public static function getLabel(): string
    {
        return 'Twitter embed';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the twitter embed block')
            ->schema([
                TextInput::make('url'),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.tiptapblocks.previews.twitter-embed', [
            //
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('filament.tiptapblocks.rendered.twitter-embed', $config)->render();
    }
}
