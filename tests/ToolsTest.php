<?php

use Digitonic\FilamentRichEditorTools\Enums\RenderType;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins\TableOfContentsPlugin;
use Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil;
use Digitonic\FilamentRichEditorTools\Tests\Fixtures\RichEditorMentionProvider;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('auto registers custom rich content plugin', function (): void {
    $renderer = RichEditorUtil::render('<p>Example</p>', RenderType::RENDERER);
    $pluginClasses = collect($renderer->getPlugins())->map(fn ($p) => $p::class);

    expect($pluginClasses)->contains(TableOfContentsPlugin::class)->toBeTrue();
});

it('Check we can access the to table of contents functions', function (): void {
    $renderer = RichEditorUtil::render('<h1>Example</h1> <p>Stuff</p> <h2>More Headers</h2>', RenderType::TOC);

    expect($renderer)->toBeArray();
    expect($renderer[0]['text'])->toBe('Example');
    expect($renderer[0]['subs'][0]['text'])->toBe('More Headers');
});

it('Check we can access the to array functions', function (): void {
    $renderer = RichEditorUtil::render('<h1>Example</h1> <p>Stuff</p> <h2>More Headers</h2>', RenderType::ARRAY);

    expect($renderer)->toBeArray();
    expect($renderer['type'])->toBe('doc');
    expect($renderer['content'])->toBeArray();
});

it('Check we can access the to html functions', function (): void {
    $renderer = RichEditorUtil::render('<h1>Example</h1> <p>Stuff</p> <h2>More Headers</h2>');

    expect($renderer)->toBeString();
    expect($renderer)->toBe('<h1>Example</h1><p>Stuff</p><h2>More Headers</h2>');
});

it('Check we can access the to text functions', function (): void {
    $renderer = RichEditorUtil::render('<h1>Example</h1>', RenderType::TEXT);

    expect($renderer)->toBeString();
    expect($renderer)->toBe('Example');
});

it('does not apply mention providers by default', function (): void {
    $editor = RichEditorUtil::make('content');
    $renderer = RichEditorUtil::render('<p>Example</p>', RenderType::RENDERER);

    expect($editor->hasMentions())->toBeFalse();
    expect($renderer->getMentionProviders())->toBeNull();
});

it('applies configured mention providers to editor instances', function (): void {
    config()->set('filament-rich-editor-tools.mention_providers', [
        RichEditorMentionProvider::class,
    ]);

    $editor = RichEditorUtil::make('content');
    $mentionProviders = (fn (): array => $this->mentions)->call($editor);

    expect($mentionProviders)->toHaveCount(1);
    expect($mentionProviders[0]->getChar())->toBe('@');
});

it('applies configured mention providers to renderer instances', function (): void {
    config()->set('filament-rich-editor-tools.mention_providers', [
        RichEditorMentionProvider::class,
    ]);

    $content = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello ',
                    ],
                    [
                        'type' => 'mention',
                        'attrs' => [
                            'id' => '1',
                            'char' => '@',
                        ],
                    ],
                ],
            ],
        ],
    ];

    $renderer = RichEditorUtil::render($content, RenderType::RENDERER);

    expect($renderer->getMentionProviders())->toHaveCount(1);
    expect($renderer->toHtml())->toContain('Jane Doe');
});
