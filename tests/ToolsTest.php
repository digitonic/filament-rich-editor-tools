<?php

use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins\TableOfContentsPlugin;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('auto registers custom rich content plugin', function (): void {
    $renderer = \Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil::render('<p>Example</p>', \Digitonic\FilamentRichEditorTools\Enums\RenderType::RENDERER);
    $pluginClasses = collect($renderer->getPlugins())->map(fn ($p) => $p::class);

    expect($pluginClasses)->contains(TableOfContentsPlugin::class)->toBeTrue();
});

it('Check we can access the to table of contents functions', function (): void {
    $renderer = \Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil::render('<h1>Example</h1> <p>Stuff</p> <h2>More Headers</h2>', \Digitonic\FilamentRichEditorTools\Enums\RenderType::TOC);

    expect($renderer)->toBeArray();
    expect($renderer[0]['text'])->toBe('Example');
    expect($renderer[0]['subs'][0]['text'])->toBe('More Headers');
});

it('Check we can access the to array functions', function (): void {
    $renderer = \Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil::render('<h1>Example</h1> <p>Stuff</p> <h2>More Headers</h2>', \Digitonic\FilamentRichEditorTools\Enums\RenderType::ARRAY);

    expect($renderer)->toBeArray();
    expect($renderer['type'])->toBe('doc');
    expect($renderer['content'])->toBeArray();
});

it('Check we can access the to html functions', function (): void {
    $renderer = \Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil::render('<h1>Example</h1> <p>Stuff</p> <h2>More Headers</h2>');

    expect($renderer)->toBeString();
    expect($renderer)->toBe('<h1>Example</h1><p>Stuff</p><h2>More Headers</h2>');
});

it('Check we can access the to text functions', function (): void {
    $renderer = \Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil::render('<h1>Example</h1>', \Digitonic\FilamentRichEditorTools\Enums\RenderType::TEXT);

    expect($renderer)->toBeString();
    expect($renderer)->toBe('Example');
});
