<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Traits;

/**
 * Main trait that provides all Eloquaint features.
 *
 * This trait combines both attribute-based relationships and scopes,
 * providing a single entry point for all Eloquaint functionality.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasEloquaintFeatures
{
    use HasAttributeRelations;
    use HasAttributeScopes;

    /**
     * Handle dynamic method calls for both relationships and scopes.
     *
     * This method first checks for attribute-defined scopes, then falls back
     * to relationship handling, and finally to the parent __call method.
     *
     * @param  string  $method  The method name
     * @param  array<mixed>  $parameters  The method parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // First, check if this is an attribute-defined scope
        if ($this->isAttributeScope($method)) {
            return $this->handleAttributeScope($method, $parameters);
        }

        // Then check if this is an attribute-defined relationship
        if ($attribute = $this->getAttributeRelation($method)) {
            return $this->resolveAttributeRelation($attribute, $method);
        }

        // Fall back to parent implementation
        return parent::__call($method, $parameters);
    }

    /**
     * Handle static method calls for attribute-defined scopes.
     *
     * This method handles static scope calls like Model::published().
     *
     * @param  string  $method  The method name
     * @param  array<mixed>  $parameters  The method parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        // Check if this is an attribute-defined scope
        if (static::isStaticAttributeScope($method)) {
            return static::handleStaticAttributeScope($method, $parameters);
        }

        // Fall back to parent implementation
        return parent::__callStatic($method, $parameters);
    }
}
