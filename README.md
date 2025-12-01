# Filament Rich Editor Tools (Filament v4)

A Laravel package that provides utilities and extensions for Filament's Rich Editor. Today, it focuses on a robust, hierarchical Table of Contents (TOC) feature built on TipTap, with automatic project-wide registration and convenient Renderer macros.

Key features
- Automatic project-wide plugin registration via the service provider.
- Generate a hierarchical Table of Contents from editor content.
- Assign stable, unique heading IDs for in-page navigation.
- Built to be compatible with Filament v4, Livewire v3, and Laravel 12.

## Requirements
- PHP 8.4+
- Laravel 12
- Filament v4 Rich Editor
- Livewire v3

## Installation

Install the package via Composer:

```bash
composer require digitonic/filament-rich-editor-tools
```

The service provider will be auto-discovered. No manual registration is needed.

## What it adds

This package adds:

1) A TipTap PHP extension (Heading ID support) automatically registered on every Filament Rich Editor renderer instance.
2) Macros on Filament's `RichContentRenderer` to work with Table of Contents data:
   - `toTableOfContents(int $maxDepth = 3): array` — returns a nested TOC array from the current renderer content.
   - `processHeaderIds(Editor $editor, int $maxDepth = 3): void` — assigns unique IDs to heading nodes inside a TipTap `Editor`.

These are registered once and applied automatically to all renderers.

## Usage

### Use our editor

In your Filament form or page, to get our rich editor do the following:

```php
RichEditorUtil::make('raw_content'),
````

### Use our Renderer

In your Filament form or page, to get our renderer do the following:

```php
RichEditorUtil::render('raw_content'),
````

### Generate a Table of Contents from content

Given a Filament `RichEditor` field or any HTML you pass to `RichContentRenderer`:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

$renderer = RichContentRenderer::make('<h1>Intro</h1><p>Text</p><h2>Details</h2>');

$toc = $renderer->toTableOfContents(maxDepth: 3);

// Example structure:
// [
//   [
//     'id' => 'intro',
//     'text' => 'Intro',
//     'depth' => 1,
//     'subs' => [
//       ['id' => 'details', 'text' => 'Details', 'depth' => 2],
//     ],
//   ],
// ]
```

The IDs are derived from the heading text and are deduplicated automatically (e.g., `intro`, `intro-1`, `intro-2`).

### Assign stable IDs to headings in an Editor

If you’re working directly with a TipTap `Editor`, you can assign IDs to its headings:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Tiptap\Core\Editor;

$editor = new Editor([
    'content' => '<h1>Intro</h1><h2>Details</h2>',
]);

RichContentRenderer::make('')->processHeaderIds($editor, maxDepth: 3);

// The editor now contains heading nodes with unique `id` attributes.
```

### Automatic plugin registration

The package service provider hooks into Laravel’s container and adds the Heading ID TipTap extension to every `RichContentRenderer` resolved by the container. You don’t need to manually add the plugin in your forms or pages.

## Configuration

At present, there is no user-facing configuration. Defaults are sensible:
- `maxDepth` defaults to 3 (h1..h3). You can override per-call.

### Custom Blocks
When using our editor/renderer you may want to implement your own custom blocks,
You can add to the custom blocks by editing the config `content_blocks` and passing in an array
of custom block classes.

## Testing

We recommend writing feature tests around your Filament pages/components and asserting the TOC output when relevant. Example (Pest):

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

it('builds a nested table of contents', function () {
    $renderer = RichContentRenderer::make('<h1>Intro</h1><h2>Details</h2><h2>More</h2>');
    $toc = $renderer->toTableOfContents();

    expect($toc)
        ->toBeArray()
        ->and($toc[0]['text'] ?? null)->toBe('Intro')
        ->and($toc[0]['subs'][0]['text'] ?? null)->toBe('Details');
});
```

Run your tests:

```bash
php artisan test --filter=table of contents
```

## FAQ

- Do I need to import or call anything to register the plugin? No — the package auto-registers via the service provider.
- Will it interfere with my existing editor setup? It only adds a stable `id` attribute to heading nodes and exposes TOC macros. It doesn’t alter your content beyond that.
- Does it work with dark mode or custom themes? Yes; the extension affects document structure, not presentation.

## Contributing

Contributions are welcome. Please open issues or PRs describing your use case and proposed changes.

## License

The MIT License (MIT). See `LICENSE.md` for details.
