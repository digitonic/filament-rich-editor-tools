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
    case UNSAFE_HTML = 5; // Use this when you need iframes or other elements that get sanitized

    public function getLabel(): string
    {
        return match ($this) {
            self::HTML => 'HTML',
            self::UNSAFE_HTML => 'Unsafe HTML',
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
        // If the content is empty, we're better just returning an empty string for now
        if (empty($renderer->getEditor()->getDocument())) {
            return '';
        }

        return match ($this) {
            self::HTML => $renderer->toHtml(),
            self::UNSAFE_HTML => $renderer->toUnsafeHtml(), // If you use iFrames, you'll need this
            self::ARRAY => $renderer->toArray(),
            self::TEXT => $renderer->toText(),
            /** @phpstan-ignore-next-line */
            self::TOC => $renderer->toTableOfContents(),
            default => $renderer,
        };
    }
}
