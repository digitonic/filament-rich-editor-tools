<?php

namespace Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins\TipTapExtensions;

use Tiptap\Core\Extension;

class IdExtension extends Extension
{
    /**
     * @var string
     */
    public static $name = 'id';

    /**
     * @return array<array<string, mixed>>
     */
    public function addGlobalAttributes(): array
    {
        return [
            [
                'types' => [
                    'heading',
                    'link',
                ],
                'attributes' => [
                    'id' => [
                        'default' => null,
                        'parseHTML' => function ($DOMNode) {
                            return $DOMNode->hasAttribute('id') ? $DOMNode->getAttribute('id') : null;
                        },
                        'renderHTML' => function ($attributes) {
                            if (! property_exists($attributes, 'id')) {
                                return null;
                            }

                            return [
                                'id' => $attributes->id,
                            ];
                        },
                    ],
                ],
            ],
        ];
    }
}
