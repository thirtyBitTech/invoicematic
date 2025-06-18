<?php

namespace Thirtybittech\Invoicematic;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Statamic\Events\EntrySaved;
use Statamic\Events\EntrySaving;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Thirtybittech\Invoicematic\Console\CreateInvoiceCollection;
use Thirtybittech\Invoicematic\Listeners\GenerateInvoiceOnOrderCreated;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        EntrySaving::class => [
            GenerateInvoiceOnOrderCreated::class,
        ],
    ];

    public function boot()
    {
        $this->registerViews();
        $this->registerTranslations();
        $this->registerEventListeners();
        $this->conditionallyRegisterConsoleCommands();
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'invoicematic');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/invoicematic.php' => config_path('invoicematic.php'),
            ], 'invoicematic-config');

            $this->publishes([
                __DIR__ . '/../resources/views/templates' => resource_path('views/vendor/invoicematic/templates'),
                __DIR__ . '/../resources/views/emails' => resource_path('views/vendor/invoicematic/emails'),
            ], 'invoicematic-views');

            Statamic::afterInstalled(function () {
                Artisan::call('vendor:publish', ['--tag' => 'invoicematic-config']);
            });
        }
    }

    private function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'invoicematic');
    }

    private function registerEventListeners(): void
    {
        Event::listen(EntrySaved::class, GenerateInvoiceOnOrderCreated::class);
    }

    private function conditionallyRegisterConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateInvoiceCollection::class,
            ]);
        }
    }

    private function mergeConfigurations(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/invoicematic.php', 'invoicematic');
    }
}
