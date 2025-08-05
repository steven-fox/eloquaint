<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Eloquaint;
use StevenFox\Eloquaint\EloquaintServiceProvider;

it('registers the Eloquaint service', function () {
    expect(app(Eloquaint::class))->toBeInstanceOf(Eloquaint::class);
});

it('registers the service provider', function () {
    $providers = app()->getLoadedProviders();
    expect($providers)->toHaveKey(EloquaintServiceProvider::class);
});

it('can access Eloquaint through the facade', function () {
    expect(\StevenFox\Eloquaint\Facades\Eloquaint::version())->toBe('1.0.0');
});

it('can clear the relationship cache', function () {
    // Create a model to populate the cache
    $author = new \StevenFox\Eloquaint\Tests\Models\Author;
    $author->posts(); // This should populate the cache

    // Clear the cache
    \StevenFox\Eloquaint\Facades\Eloquaint::clearCache();

    // The cache should be empty now
    $cached = \StevenFox\Eloquaint\Facades\Eloquaint::getCachedRelationships(\StevenFox\Eloquaint\Tests\Models\Author::class);
    expect($cached)->toBeEmpty();
});
