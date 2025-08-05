<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider for the Eloquaint package.
 *
 * Registers the package with Laravel and sets up any necessary bindings.
 */
final class EloquaintServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     *
     * @param  Package  $package  The package instance to configure
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('eloquaint')
            ->hasConfigFile();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        // Register the main Eloquaint class as a singleton
        $this->app->singleton(Eloquaint::class, function () {
            return new Eloquaint;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
