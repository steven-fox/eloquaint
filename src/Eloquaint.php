<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint;

/**
 * Main Eloquaint class for package functionality.
 *
 * This class provides utility methods and serves as the main entry point
 * for the Eloquaint package functionality.
 */
final readonly class Eloquaint
{
    /**
     * Get the package version.
     *
     * @return string The current package version
     */
    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Clear all attribute caches (relationships and scopes).
     *
     * This method can be useful during testing or when models are
     * dynamically modified at runtime.
     */
    public function clearCache(): void
    {
        $this->clearRelationsCache();
        $this->clearScopesCache();
    }

    /**
     * Clear the attribute relations cache.
     */
    public function clearRelationsCache(): void
    {
        // Access the cache through reflection since it's protected
        $reflection = new \ReflectionClass(\StevenFox\Eloquaint\Traits\HasAttributeRelations::class);
        $property = $reflection->getProperty('attributeRelationsCache');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    /**
     * Clear the attribute scopes cache.
     */
    public function clearScopesCache(): void
    {
        // Access the cache through reflection since it's protected
        $reflection = new \ReflectionClass(\StevenFox\Eloquaint\Traits\HasAttributeScopes::class);
        $property = $reflection->getProperty('attributeScopesCache');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    /**
     * Get cached relationships for a model class.
     *
     * @param  string  $modelClass  The model class name
     * @return array<string, \StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute>
     */
    public function getCachedRelationships(string $modelClass): array
    {
        $reflection = new \ReflectionClass(\StevenFox\Eloquaint\Traits\HasAttributeRelations::class);
        $property = $reflection->getProperty('attributeRelationsCache');
        $property->setAccessible(true);
        $cache = $property->getValue();

        return $cache[$modelClass] ?? [];
    }

    /**
     * Get cached scopes for a model class.
     *
     * @param  string  $modelClass  The model class name
     * @return array<string, \StevenFox\Eloquaint\Attributes\Scope>
     */
    public function getCachedScopes(string $modelClass): array
    {
        $reflection = new \ReflectionClass(\StevenFox\Eloquaint\Traits\HasAttributeScopes::class);
        $property = $reflection->getProperty('attributeScopesCache');
        $property->setAccessible(true);
        $cache = $property->getValue();

        return $cache[$modelClass] ?? [];
    }
}
