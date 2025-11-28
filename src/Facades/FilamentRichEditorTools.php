<?php

namespace Digitonic\FilamentRichEditorTools\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Digitonic\FilamentRichEditorTools\FilamentRichEditorTools
 */
class FilamentRichEditorTools extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Digitonic\FilamentRichEditorTools\FilamentRichEditorTools::class;
    }
}
