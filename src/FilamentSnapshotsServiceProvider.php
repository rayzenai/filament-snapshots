<?php

declare(strict_types=1);

namespace Rayzenai\FilamentSnapshots;

use Rayzenai\FilamentSnapshots\Models\ContentSnapshot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class FilamentSnapshotsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-snapshots.php', 'filament-snapshots');
    }

    public function boot(): void
    {
        $this->registerMigrations();
        $this->registerViews();
        $this->registerCommands();
        $this->registerPublishing();
        $this->registerMorphMap();
        $this->registerLivewireComponents();
    }

    protected function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-snapshots');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            // Register artisan commands here if needed
        }
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__ . '/../config/filament-snapshots.php' => config_path('filament-snapshots.php'),
            ], 'filament-snapshots-config');

            // Migrations
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'filament-snapshots-migrations');

            // Views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-snapshots'),
            ], 'filament-snapshots-views');
        }
    }

    protected function registerMorphMap(): void
    {
        Relation::morphMap([
            'content_snapshot' => ContentSnapshot::class,
        ]);
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('filament-snapshots::content-snapshots-modal', \Rayzenai\FilamentSnapshots\Livewire\ContentSnapshotsModal::class);
    }
}