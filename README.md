# Filament Rich Editor Tools (Filament v4)


This package integrates deeply with Filament 4's Rich Editor via macros and service provider hooks, effectively taking over and enhancing much of the core Rich Editor functionality to provide a more feature-rich experience.

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

## Overview

This package extends Filament v4's Rich Editor through a comprehensive integration that:

- **Automatically enhances all Rich Editor instances** with additional functionality via Laravel's service container
- **Provides a unified `RichEditorUtil` class** that ensures your editor and renderer configurations stay synchronized  
- **Registers custom blocks globally** so they're available in any Rich Editor field
- **Adds macro methods** to `RichContentRenderer` for advanced content processing
- **Handles heading ID assignment** automatically for better SEO and navigation

The integration is designed to be seamless - simply replace your existing `RichEditor::make()` calls with `RichEditorUtil::make()` to access all enhanced features.

## What it adds

This package enhances Filament's Rich Editor through automatic registration and macros:

1. **TipTap PHP extensions** automatically registered on every Filament Rich Editor renderer instance:
   - Heading ID support for automatic anchor generation
   - Enhanced table of contents generation capabilities

2. **Macros on Filament's `RichContentRenderer`** for advanced content processing:
   - `toTableOfContents(int $maxDepth = 3): array` — returns a nested TOC array from the current renderer content
   - `processHeaderIds(Editor $editor, int $maxDepth = 3): void` — assigns unique IDs to heading nodes inside a TipTap `Editor`

3. **Four production-ready custom blocks** (detailed below):
   - **Pros & Cons Block**: Side-by-side comparison lists with icons
   - **Video Block**: Embeddable YouTube and Vimeo videos with captions  
   - **Twitter Embed Block**: Native Twitter/X post embedding
   - **Table of Contents Block**: Dynamic, navigable content outline

4. **Migration command** for legacy content conversion:
   - `php artisan filament-rich-editor-tools:migrate-blocks` to convert old TipTap blocks to Rich Editor format

5. **Unified Editor/Renderer utilities** that ensure consistency between editing and display modes

These features are registered globally and applied automatically to all Rich Editor instances in your application.

## Usage

### Convert your blocks
```php 
php artisan filament-rich-editor-tools:migrate-blocks "App\Models\Page" blocks
````

Replace the model and field name as needed. This will overwrite existing DB records. So take a backup.

If you have a complex JSON structure with your rich editor located on something like `meta.content` you can use dot notation to specify the field.

```php 
php artisan filament-rich-editor-tools:migrate-blocks "App\Models\Page" meta.content
````


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

## Custom Blocks

This package ships with four production-ready custom blocks that extend the Rich Editor's functionality:

### 1. Pros & Cons Block

Creates visually appealing side-by-side comparison lists with checkmark and X icons.

**Features:**
- Separate repeatable fields for pros and cons
- Green/red color coding with appropriate icons
- Responsive two-column layout
- Collapsible interface in the editor

**Editor Configuration:**
- Add unlimited pros and cons via repeater fields
- Each item has a simple text input
- Items are collapsible and cloneable for easy management

**Output:**
- Clean, professional layout with branded colors
- Checkmark icons (✓) for pros, X icons (✗) for cons
- Responsive design that stacks on mobile

### 2. Video Block

Embeds YouTube and Vimeo videos with full responsive support.

**Features:**
- Automatic detection of YouTube and Vimeo URLs
- Responsive iframe embedding
- Optional caption support
- Secure iframe attributes (no-referrer, allowfullscreen)

**Supported Platforms:**
- YouTube (youtube.com, youtu.be)
- Vimeo (vimeo.com)
- Requires YouTube Watch Links, not embed links.

**Editor Configuration:**
- URL field for the video link
- Optional caption field

**Output:**
- Full-width responsive video player
- 16:9 aspect ratio maintained
- Caption displayed below video if provided

### 3. Twitter Embed Block

Embeds Twitter/X posts using Twitter's native embedding system.

**Features:**
- Native Twitter widget integration
- Automatic URL conversion (x.com → twitter.com)
- Async script loading for performance

**Editor Configuration:**
- Single URL field for the Twitter/X post

**Output:**
- Native Twitter blockquote with full interactivity
- Preserves original post styling and functionality
- Responsive design

### 4. Table of Contents Block

Dynamically generates a navigable table of contents from the current document.

**Features:**
- Automatic generation from document headings
- Hierarchical structure with proper indentation
- Click-to-navigate functionality
- Customizable depth levels

**Editor Configuration:**
- Automatically captures the current model and field information
- No manual configuration required

**Output:**
- Nested list structure with proper hierarchy
- Hover effects and visual feedback
- Smooth scroll navigation to sections
- Responsive indentation based on heading depth

### Using Custom Blocks

All custom blocks are automatically registered when you use `RichEditorUtil::make()`:

```php
use Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil;

// In your Filament form
RichEditorUtil::make('content')
    ->label('Page Content'),
```

Blocks appear in the "Custom Blocks" toolbar button and can be inserted anywhere in your content.

### Adding Your Own Custom Blocks

You can extend the available blocks by adding them to the configuration:

```php
// config/filament-rich-editor-tools.php
return [
    'custom_blocks' => [
        App\Filament\CustomBlocks\MyCustomBlock::class,
        App\Filament\CustomBlocks\AnotherBlock::class,
    ],
];
```

Each custom block should extend `Filament\Forms\Components\RichEditor\RichContentCustomBlock` and implement:
- `getId()`: Unique identifier for the block
- `getLabel()`: Display name in the editor
- `configureEditorAction()`: Form fields for block configuration  
- `toPreviewHtml()`: HTML shown in the editor
- `toHtml()`: Final rendered HTML output

### Automatic plugin registration

The package service provider hooks into Laravel’s container and adds the Heading ID TipTap extension to every `RichContentRenderer` resolved by the container. You don’t need to manually add the plugin in your forms or pages.

## Configuration

Publish the configuration file to customize the package behavior:

```bash
php artisan vendor:publish --tag=filament-rich-editor-tools-config
```

### Available Configuration Options

```php
return [
    // Table of Contents settings
    'table_of_contents' => [
        'enabled' => true,
        'prefix' => '', // Add a prefix to all heading IDs (e.g., 'section-')
    ],

    // Add your own custom blocks
    'custom_blocks' => [
        App\Filament\CustomBlocks\CalloutBlock::class,
        App\Filament\CustomBlocks\CodeSnippetBlock::class,
        // Add as many as needed
    ],
];
```

### Configuration Details

- **`table_of_contents.enabled`**: Controls whether TOC functionality is active
- **`table_of_contents.prefix`**: Adds a prefix to all generated heading IDs for namespace separation
- **`custom_blocks`**: Array of custom block classes that extend the available blocks in your Rich Editor

The package automatically merges your custom blocks with the four built-in blocks (Pros & Cons, Video, Twitter Embed, Table of Contents).

## Testing

We recommend writing feature tests around your Filament pages/components and asserting the TOC output when relevant.

### Testing Table of Contents Generation

Example using Pest:

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

### Testing Custom Blocks in Filament

```php
use Digitonic\FilamentRichEditorTools\Filament\Utilities\RichEditorUtil;

it('can render pros and cons block', function () {
    $content = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'prosAndConsBlock',
                'attrs' => [
                    'pros' => [['text' => 'Great performance']],
                    'cons' => [['text' => 'Expensive']]
                ]
            ]
        ]
    ];

    $rendered = RichEditorUtil::render($content);
    
    expect($rendered)
        ->toContain('Great performance')
        ->toContain('Expensive');
});
```

### Testing Video Embedding

```php
use Digitonic\FilamentRichEditorTools\Support\EmbeddableVideo;

it('can embed youtube videos', function () {
    $video = new EmbeddableVideo('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    
    expect($video->isEmbeddable())->toBeTrue();
    expect($video->getEmbedUrl())->toContain('youtube.com/embed/');
});
```

Run your tests:

```bash
php artisan test --filter="rich editor"
```

## Contributing

Contributions are welcome. Please open issues or PRs describing your use case and proposed changes.

## License

The MIT License (MIT). See `LICENSE.md` for details.
