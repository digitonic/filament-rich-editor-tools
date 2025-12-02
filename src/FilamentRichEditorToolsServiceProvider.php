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
        $this->app->afterResolving(RichContentRenderer::class, function (RichContentRenderer $renderer): void {
            $alreadyRegistered = collect($renderer->getPlugins())
                ->contains(fn ($plugin) => $plugin instanceof TableOfContentsPlugin);

            if (! $alreadyRegistered) {
                $renderer->plugins([app(TableOfContentsPlugin::class)]);
            }
        });

        // adjust the path if your service provider sits in src/ — this assumes src/ is one level deep
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-rich-editor-tools');

        // optional publish
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament-rich-editor-tools'),
        ], 'views');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilamentRichEditorToolsCommand::class,
            ]);
        }

        RichContentRendererMacros::register();
    }

    public function configurePackage(Package $package): void
    {

        $package
            ->name('filament-rich-editor-tools')
            ->hasConfigFile();
    }
}
