<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

class ProsAndConsBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'pros_and_cons';
    }

    public static function getLabel(): string
    {
        return 'Pros andcons';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalDescription('Configure the pros andcons block')
            ->schema([
                Repeater::make('pros')
                    ->schema([
                        TextInput::make('text')
                            ->label('Text')
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                    ])
                    ->addActionLabel('Add Pro')
                    ->collapsible()
                    ->collapsed()
                    ->cloneable()
                    ->columns(2)
                    ->defaultItems(0)
                    ->itemLabel(fn (array $state): ?string => $state['text'] ?? null),

                Repeater::make('cons')
                    ->schema([
                        TextInput::make('text')
                            ->label('Text')
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                    ])
                    ->defaultItems(0)
                    ->addActionLabel('Add Con')
                    ->collapsible()
                    ->collapsed()
                    ->cloneable()
                    ->columns(2)
                    ->itemLabel(fn (array $state): ?string => $state['text'] ?? null),
            ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        /** @var view-string $view */
        $view = 'filament-rich-editor-tools::filament.rich-editor-custom-blocks.previews.pros-cons';

        return view($view, $config)
            ->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        /** @var view-string $view */
        $view = 'filament-rich-editor-tools::filament.rich-editor-custom-blocks.rendered.pros-cons';

        return view($view, $config)
            ->render();
    }
}
