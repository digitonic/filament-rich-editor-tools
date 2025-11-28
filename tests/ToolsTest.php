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
