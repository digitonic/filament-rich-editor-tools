<?php

namespace Digitonic\FilamentRichEditorTools;

use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins\TableOfContentsPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Digitonic\FilamentRichEditorTools\Commands\FilamentRichEditorToolsCommand;

class FilamentRichEditorToolsServiceProvider extends PackageServiceProvider
{

    public function boot(): void
    {
        $this->app->afterResolving(RichContentRenderer::class, function (RichContentRenderer $renderer): void {
            $alreadyRegistered = collect($renderer->getPlugins())
                ->contains(fn ($plugin) => $plugin instanceof TableOfContentsPlugin);

            if (! $alreadyRegistered) {
                $renderer->plugins([app(TableOfContentsPlugin::class)]);
            }
        });

    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-rich-editor-tools')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(FilamentRichEditorToolsCommand::class);
    }
}
