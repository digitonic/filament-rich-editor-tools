<?php

namespace Digitonic\FilamentRichEditorTools\Database\Factories\Concerns;

trait BuildsRichEditorContent
{
    /**
     * @param  array<int, array<string, mixed>>  $nodes
     * @return array<string, mixed>
     */
    protected function richEditorDocument(array $nodes): array
    {
        return [
            'type' => 'doc',
            'content' => $nodes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function richEditorParagraph(?string $text = null): array
    {
        $paragraph = [
            'type' => 'paragraph',
        ];

        if ($text !== null) {
            $paragraph['content'] = [
                [
                    'type' => 'text',
                    'text' => $text,
                ],
            ];
        }

        return $paragraph;
    }

    /**
     * @return array<string, mixed>
     */
    protected function richEditorParagraphs(int $count = 1): array
    {
        $paragraphs = $this->faker->paragraphs($count);

        return $this->richEditorDocument(
            array_map(
                fn (string $paragraph): array => $this->richEditorParagraph($paragraph),
                $paragraphs,
            ),
        );
    }
}
