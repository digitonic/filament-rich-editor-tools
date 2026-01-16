<?php

namespace Digitonic\FilamentRichEditorTools;

use Digitonic\FilamentRichEditorTools\Commands\FilamentRichEditorToolsCommand;
use Digitonic\FilamentRichEditorTools\Filament\Forms\Components\RichEditor\Plugins\TableOfContentsPlugin;
use Digitonic\FilamentRichEditorTools\Support\RichContentRendererMacros;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentRichEditorToolsServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        // Rich Content Plugins
        $this->app->afterResolving(RichContentRenderer::class, function (RichContentRenderer $renderer): void {
            $alreadyRegistered = collect($renderer->getPlugins())
                ->contains(fn ($plugin) => $plugin instanceof TableOfContentsPlugin);

            if (! $alreadyRegistered) {
                $renderer->plugins([app(TableOfContentsPlugin::class)]);
            }
        });

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-rich-editor-tools');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament-rich-editor-tools'),
        ], 'filament-rich-editor-tools-views');

        // Config
        $this->publishes([
            __DIR__.'/../config/filament-rich-editor-tools.php' => config_path('filament-rich-editor-tools.php'),
        ], 'filament-rich-editor-tools-config');

        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilamentRichEditorToolsCommand::class,
            ]);
        }

        // Table of Contents Macros
        RichContentRendererMacros::register();
    }

    public function configurePackage(Package $package): void
    {

        $package
            ->name('filament-rich-editor-tools');
    }
}
