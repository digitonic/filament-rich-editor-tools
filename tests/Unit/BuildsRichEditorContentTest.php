<?php

use Digitonic\FilamentRichEditorTools\Enums\RenderType;
use Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil;
use Digitonic\FilamentRichEditorTools\Tests\Fixtures\BuildsRichEditorContentHarness;
use Faker\Factory as FakerFactory;

it('builds a rich editor document wrapper', function (): void {
    $harness = new BuildsRichEditorContentHarness(FakerFactory::create());

    $nodes = [
        [
            'type' => 'paragraph',
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'Hello',
                ],
            ],
        ],
    ];

    expect($harness->makeDocument($nodes))->toBe([
        'type' => 'doc',
        'content' => $nodes,
    ]);
});

it('builds an empty rich editor paragraph when text is null', function (): void {
    $harness = new BuildsRichEditorContentHarness(FakerFactory::create());

    expect($harness->makeParagraph())->toBe([
        'type' => 'paragraph',
    ]);
});

it('builds a rich editor paragraph with text content', function (): void {
    $harness = new BuildsRichEditorContentHarness(FakerFactory::create());

    expect($harness->makeParagraph('Hello'))->toBe([
        'type' => 'paragraph',
        'content' => [
            [
                'type' => 'text',
                'text' => 'Hello',
            ],
        ],
    ]);
});

it('builds one faker paragraph in a rich editor document', function (): void {
    $harness = new BuildsRichEditorContentHarness(FakerFactory::create());

    $document = $harness->makeParagraphs();

    expect($document)->toBeArray()
        ->and($document['type'])->toBe('doc')
        ->and($document['content'])->toHaveCount(1)
        ->and($document['content'][0]['type'] ?? null)->toBe('paragraph')
        ->and($document['content'][0]['content'][0]['type'] ?? null)->toBe('text')
        ->and($document['content'][0]['content'][0]['text'] ?? null)->toBeString()
        ->and($document['content'][0]['content'][0]['text'] ?? null)->not->toBe('');
});

it('builds multiple faker paragraphs in a rich editor document', function (): void {
    $harness = new BuildsRichEditorContentHarness(FakerFactory::create());

    $document = $harness->makeParagraphs(3);

    expect($document)->toBeArray()
        ->and($document['type'])->toBe('doc')
        ->and($document['content'])->toHaveCount(3);

    foreach ($document['content'] as $node) {
        expect($node['type'] ?? null)->toBe('paragraph')
            ->and($node['content'][0]['type'] ?? null)->toBe('text')
            ->and($node['content'][0]['text'] ?? null)->toBeString()
            ->and($node['content'][0]['text'] ?? null)->not->toBe('');
    }
});

it('renders trait-generated rich editor content with expected output', function (): void {
    $harness = new BuildsRichEditorContentHarness(FakerFactory::create());

    $content = $harness->makeDocument([
        $harness->makeParagraph('Renderer line one'),
        $harness->makeParagraph('Renderer line two'),
    ]);

    $html = RichEditorUtil::render($content, RenderType::HTML);
    $text = RichEditorUtil::render($content, RenderType::TEXT);

    expect($html)->toBe('<p>Renderer line one</p><p>Renderer line two</p>')
        ->and($text)->toContain('Renderer line one')
        ->and($text)->toContain('Renderer line two');
});
