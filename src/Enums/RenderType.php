<?php

namespace Digitonic\FilamentRichEditorTools\Enums;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Support\Contracts\HasLabel;

enum RenderType: int implements HasLabel
{
    case HTML = 0;
    case TEXT = 1;
    case ARRAY = 2;
    case TOC = 3;
    case RENDERER = 4; // This is for cases when you just want the renderer instance back.

    public function getLabel(): string
    {
        return match ($this) {
            self::HTML => 'HTML',
            self::ARRAY => 'Array',
            self::TEXT => 'Text',
            self::TOC => 'Table of Contents',
            default => 'Rich Content Renderer',
        };
    }

    /**
     * Get the render method for the given RichContentRenderer based on the RenderType.
     */
    public function getRenderMethod(RichContentRenderer $renderer): mixed
    {
        return match ($this) {
            self::HTML => $renderer->toHtml(),
            self::ARRAY => $renderer->toArray(),
            self::TEXT => $renderer->toText(),
            self::TOC => $renderer->toTableOfContents(),
            default => $renderer,
        };
    }
}
