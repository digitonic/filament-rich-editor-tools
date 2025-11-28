<?php

namespace Digitonic\FilamentRichEditorTools\Commands;

use Illuminate\Console\Command;

class FilamentRichEditorToolsCommand extends Command
{
    public $signature = 'filament-rich-editor-tools';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
