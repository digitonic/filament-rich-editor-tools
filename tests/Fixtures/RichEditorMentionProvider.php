<?php

namespace Digitonic\FilamentRichEditorTools\Tests\Fixtures;

use Digitonic\FilamentRichEditorTools\Contracts\ProvidesRichEditorMentionProviders;
use Filament\Forms\Components\RichEditor\MentionProvider;

class RichEditorMentionProvider implements ProvidesRichEditorMentionProviders
{
    public function getMentionProviders(): array
    {
        return [
            MentionProvider::make('@')
                ->items([
                    '1' => 'Jane Doe',
                ]),
        ];
    }
}
