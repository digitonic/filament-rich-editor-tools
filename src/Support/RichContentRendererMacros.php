<?php

namespace Digitonic\FilamentRichEditorTools\Support;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Str;
use Tiptap\Editor;

class RichContentRendererMacros
{
    public static function register(): void
    {
        // Generate hierarchical Table of Contents from current renderer content.
        RichContentRenderer::macro('toTableOfContents', function (int $maxDepth = 3): array {
            /** @var RichContentRenderer $this */
            if (empty($this->content)) {
                return [];
            }

            $document = $this->toArray();

            if (empty($document['content'])) {
                return [];
            }

            $idCounts = [];
            $headings = RichContentRendererMacros::parseTOCHeadings($document['content'], $maxDepth, $idCounts);

            if ($headings === []) {
                return [];
            }

            return RichContentRendererMacros::generateTOCArray($headings);
        });

        // Assign unique ids to heading nodes within an Editor instance up to a max depth.
        RichContentRenderer::macro('processHeaderIds', function (int $maxDepth = 3): RichContentRenderer {
            $editor = $this->getEditor();
            /** @var RichContentRenderer $this */
            $idCounts = [];

            $editor->descendants(function (&$node) use ($maxDepth, &$idCounts): void {
                if ($node->type !== 'heading') {
                    return;
                }

                if ($node->attrs->level > $maxDepth) {
                    return;
                }

                $baseId = str(collect($node->content)->map(function ($child) {
                    return $child->text ?? null;
                })->implode(' '))->slug()->toString();

                if ($baseId === '') {
                    $baseId = 'heading';
                }

                if (isset($idCounts[$baseId])) {
                    $idCounts[$baseId]++;
                    $uniqueId = $baseId.'-'.$idCounts[$baseId];
                } else {
                    $idCounts[$baseId] = 0;
                    $uniqueId = $baseId;
                }

                $node->attrs->id = $uniqueId;
            });

            $content = $editor->getDocument();
            $this->content($content);

            return $this;
        });

        /**
         * Actually create the # reference in the text
         */
        RichContentRenderer::macro('parseHeadings', function (int $maxDepth = 3): RichContentRenderer {
            $editor = $this->getEditor();

            $editor->descendants(function (&$node) use ($maxDepth) {
                if ($node->type !== 'heading' || $node->attrs->level > $maxDepth) {
                    return;
                }

                array_unshift($node->content, (object) [
                    'type' => 'text',
                    'text' => '#',
                    'marks' => [
                        [
                            'type' => 'link',
                            'attrs' => [
                                'href' => '#'.$node->attrs->id,
                            ],
                        ],
                    ],
                ]);
            });

            $content = $editor->getDocument();
            $this->content($content);

            return $this;
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     * @param  array<string, int>  $idCounts
     * @return array<int, array{level:int, id:string, text:string}>
     */
    public static function parseTOCHeadings(array $nodes, int $maxDepth = 3, array &$idCounts = []): array
    {
        $headings = [];

        foreach ($nodes as $node) {
            if (! isset($node['type'])) {
                continue;
            }

            if ($node['type'] === 'heading' && isset($node['attrs']['level']) && $node['attrs']['level'] <= $maxDepth) {
                $text = trim(self::extractPlainTextFromNode($node['content'] ?? []));

                $baseId = empty($text) ? 'heading' : Str::slug($text);

                if (isset($idCounts[$baseId])) {
                    $idCounts[$baseId]++;
                    $uniqueId = $baseId.'-'.$idCounts[$baseId];
                } else {
                    $idCounts[$baseId] = 0;
                    $uniqueId = $baseId;
                }

                $headings[] = [
                    'level' => (int) $node['attrs']['level'],
                    'id' => $uniqueId,
                    'text' => $text,
                ];
            }

            if (isset($node['content']) && is_array($node['content'])) {
                $headings = [
                    ...$headings,
                    ...self::parseTOCHeadings($node['content'], $maxDepth, $idCounts),
                ];
            }
        }

        return $headings;
    }

    /**
     * @param  array<int, array{level:int, id:string, text:string}>  $headings
     * @return array<int, array{id:string, text:string, depth:int, subs?:array<int, array<string, mixed>>}>
     */
    public static function generateTOCArray(array &$headings, int $parentLevel = 0): array
    {
        $result = [];

        while ($headings !== []) {
            $current = $headings[0];
            $currentLevel = $current['level'];

            if ($parentLevel >= $currentLevel) {
                break;
            }

            array_shift($headings);

            $nextLevel = $headings[0]['level'] ?? 0;

            $entry = [
                'id' => $current['id'],
                'text' => $current['text'],
                'depth' => $currentLevel,
            ];

            if ($nextLevel > $currentLevel) {
                $entry['subs'] = self::generateTOCArray($headings, $currentLevel);
            }

            $result[] = $entry;
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     */
    public static function extractPlainTextFromNode(array $nodes): string
    {
        $buffer = '';

        foreach ($nodes as $n) {
            if (isset($n['text']) && is_string($n['text'])) {
                $buffer .= $n['text'].' ';
            }

            if (isset($n['content']) && is_array($n['content'])) {
                $buffer .= self::extractPlainTextFromNode($n['content']).' ';
            }
        }

        return trim(preg_replace('/\s+/', ' ', $buffer) ?? '');
    }
}
