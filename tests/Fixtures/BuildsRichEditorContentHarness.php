<?php

namespace Digitonic\FilamentRichEditorTools\Tests\Fixtures;

use Digitonic\FilamentRichEditorTools\Database\Factories\Concerns\BuildsRichEditorContent;
use Faker\Generator;

class BuildsRichEditorContentHarness
{
    use BuildsRichEditorContent;

    public function __construct(
        public Generator $faker,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     * @return array<string, mixed>
     */
    public function makeDocument(array $nodes): array
    {
        return $this->richEditorDocument($nodes);
    }

    /**
     * @return array<string, mixed>
     */
    public function makeParagraph(?string $text = null): array
    {
        return $this->richEditorParagraph($text);
    }

    /**
     * @return array<string, mixed>
     */
    public function makeParagraphs(int $count = 1): array
    {
        return $this->richEditorParagraphs($count);
    }
}
