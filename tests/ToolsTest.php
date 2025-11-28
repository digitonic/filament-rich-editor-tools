<?php

use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins\TableOfContentsPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('auto registers custom rich content plugin', function (): void {
    $renderer = RichContentRenderer::make('<p>Example</p>');
    $pluginClasses = collect($renderer->getPlugins())->map(fn ($p) => $p::class);

    expect($pluginClasses)->contains(TableOfContentsPlugin::class)->toBeTrue();
});

it('Check we can access the table of contents functions', function (): void {
    $renderer = RichContentRenderer::make('<h1>Example</h1> <p>STuff</p> <h2>More Headers</h2>');
    $tableOfContents = $renderer->toTableOfContents();

    expect($tableOfContents)->toBeArray();
    expect($tableOfContents[0]['text'])->toBe('Example');
    expect($tableOfContents[0]['subs'][0]['text'])->toBe('More Headers');
});
