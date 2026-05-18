<?php

namespace Digitonic\FilamentRichEditorTools\Contracts;

use Filament\Forms\Components\RichEditor\MentionProvider;

interface ProvidesRichEditorMentionProviders
{
    /**
     * @return array<MentionProvider>
     */
    public function getMentionProviders(): array;
}
