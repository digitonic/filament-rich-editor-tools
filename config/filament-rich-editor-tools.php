<?php

// config for Digitonic/FilamentRichEditorTools
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ProsAndConsBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\TableContentsBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\TwitterEmbedBlock;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\VideoBlock;

return [
    'table_of_contents' => [
        'enabled' => true,
        'prefix' => '', // This prefix determines if your heading IDs will have a prefix or not
    ],

    // Custom Blocks that will be passed into the editor, you can add or remove the existing ones.
    'custom_blocks' => [
        TwitterEmbedBlock::class,
        VideoBlock::class,
        ProsAndConsBlock::class,
        TableContentsBlock::class,
    ],

];
