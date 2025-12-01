<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class ButtonBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'button';
    }

    public static function getLabel(): string
    {
        return 'Button';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the button block')
            ->schema([
                ColorPicker::make('background_color')
                    ->default('#0FF288')
                    ->helperText('The color of the button'),
                TextInput::make('button_text')
                    ->helperText('The text inside the button'),
                ColorPicker::make('text_color')
                    ->default('#292F3A')
                    ->helperText('The color of the text inside the button'),
                TextInput::make('url')
                    ->helperText('The URL the button should link to'),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return view('filament.tiptapblocks.previews.button', [
            //
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return view('filament.tiptapblocks.rendered.button', $config)->render();
    }
}
