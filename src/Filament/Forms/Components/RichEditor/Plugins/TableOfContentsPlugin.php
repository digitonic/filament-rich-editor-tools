<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins;

use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins\TipTapExtensions\IdExtension;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Tiptap\Core\Extension;

class TableOfContentsPlugin implements RichContentPlugin
{
    /**
     * @return array<int, Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(IdExtension::class),
        ];
    }

    public function getTipTapJsExtensions(): array
    {
        return [];
    }

    public function getEditorTools(): array
    {
        return [];
    }

    public function getEditorActions(): array
    {
        return [];
    }
}
