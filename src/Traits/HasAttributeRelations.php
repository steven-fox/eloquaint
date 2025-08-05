<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use ReflectionClass;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;
use StevenFox\Eloquaint\Exceptions\InvalidRelationshipAttributeException;

/**
 * Trait for handling Eloquent relationships defined via PHP attributes.
 *
 * This trait allows models to define relationships using PHP attributes
 * instead of traditional methods, reducing boilerplate code.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasAttributeRelations
{
    /**
     * Cache for resolved attribute relationships.
     *
     * @var array<string, array<string, RelationshipAttribute>>
     */
    protected static array $attributeRelationsCache = [];

    /**
     * Boot the HasAttributeRelations trait.
     *
     * This method is called automatically by Eloquent when the model is booted.
     */
    protected static function bootHasAttributeRelations(): void
    {
        static::resolveAttributeRelations();
    }

    /**
     * Resolve and cache all attribute relationships for this model.
     *
     * @throws InvalidRelationshipAttributeException
     */
    protected static function resolveAttributeRelations(): void
    {
        $class = static::class;

        if (isset(static::$attributeRelationsCache[$class])) {
            return;
        }

        $reflection = new ReflectionClass($class);
        static::$attributeRelationsCache[$class] = [];

        // Parse class-level attributes
        foreach ($reflection->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();

            if ($instance instanceof RelationshipAttribute) {
                $relationName = static::resolveRelationName($instance);
                static::$attributeRelationsCache[$class][$relationName] = $instance;
            }
        }

        // Parse property-level attributes
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();

                if ($instance instanceof RelationshipAttribute) {
                    $relationName = $instance->getName() ?? $property->getName();
                    static::$attributeRelationsCache[$class][$relationName] = $instance;
                }
            }
        }
    }

    /**
     * Resolve the relationship name from an attribute.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return string The resolved relationship name
     */
    protected static function resolveRelationName(RelationshipAttribute $attribute): string
    {
        if ($attribute->getName() !== null) {
            return $attribute->getName();
        }

        // Generate name from related class
        $baseName = class_basename($attribute->getRelated());

        // Pluralize for "many" relationships
        if (in_array($attribute->getRelationshipType(), ['hasMany', 'belongsToMany', 'morphMany', 'morphToMany'], true)) {
            return Str::camel(Str::plural($baseName));
        }

        return Str::camel($baseName);
    }

    /**
     * Handle dynamic method calls for attribute relationships.
     *
     * @param  string  $method  The method name
     * @param  array<mixed>  $parameters  The method parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($attribute = $this->getAttributeRelation($method)) {
            return $this->resolveAttributeRelation($attribute);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Handle dynamic property access for attribute relationships.
     *
     * @param  string  $key  The property key
     * @return mixed
     */
    public function __get($key)
    {
        if ($attribute = $this->getAttributeRelation($key)) {
            return $this->getAttributeRelationValue($key);
        }

        return parent::__get($key);
    }

    /**
     * Get the value of an attribute relationship.
     *
     * @param  string  $key  The relationship key
     * @return mixed
     */
    protected function getAttributeRelationValue(string $key)
    {
        // Check if the relationship is already loaded
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // Get the relationship instance and execute it
        $attribute = $this->getAttributeRelation($key);
        if ($attribute) {
            $relation = $this->resolveAttributeRelation($attribute);

            return $this->relations[$key] = $relation->getResults();
        }

        return null;
    }

    /**
     * Get an attribute relationship by name.
     *
     * @param  string  $name  The relationship name
     */
    protected function getAttributeRelation(string $name): ?RelationshipAttribute
    {
        return static::$attributeRelationsCache[static::class][$name] ?? null;
    }

    /**
     * Override Laravel's getRelationValue to handle attribute relationships.
     *
     * @param  string  $key  The relationship key
     * @return mixed
     */
    public function getRelationValue($key)
    {
        // Check if this is an attribute relationship
        if ($attribute = $this->getAttributeRelation($key)) {
            return $this->getAttributeRelationValue($key);
        }

        // Fall back to parent implementation for regular relationships
        return parent::getRelationValue($key);
    }

    /**
     * Resolve an attribute relationship to an Eloquent relation.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     *
     * @throws InvalidRelationshipAttributeException
     */
    protected function resolveAttributeRelation(RelationshipAttribute $attribute): Relation
    {
        $relation = match ($attribute->getRelationshipType()) {
            'hasMany' => $this->resolveHasMany($attribute),
            'hasOne' => $this->resolveHasOne($attribute),
            'belongsTo' => $this->resolveBelongsTo($attribute),
            'belongsToMany' => $this->resolveBelongsToMany($attribute),
            'hasManyThrough' => $this->resolveHasManyThrough($attribute),
            'hasOneThrough' => $this->resolveHasOneThrough($attribute),
            'morphMany' => $this->resolveMorphMany($attribute),
            'morphOne' => $this->resolveMorphOne($attribute),
            'morphTo' => $this->resolveMorphTo($attribute),
            'morphToMany' => $this->resolveMorphToMany($attribute),
            default => throw new InvalidRelationshipAttributeException(
                "Unsupported relationship type: {$attribute->getRelationshipType()}"
            ),
        };

        // Apply where constraints if any
        foreach ($attribute->getWhereConstraints() as $column => $value) {
            $relation->where($column, $value);
        }

        return $relation;
    }

    /**
     * Resolve a HasMany relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return HasMany<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveHasMany(RelationshipAttribute $attribute): HasMany
    {
        /** @var \StevenFox\Eloquaint\Attributes\HasMany $attribute */

        return $this->hasMany(
            $attribute->getRelated(),
            $attribute->foreignKey,
            $attribute->localKey,
        );
    }

    /**
     * Resolve a HasOne relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return HasOne<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveHasOne(RelationshipAttribute $attribute): HasOne
    {
        /** @var \StevenFox\Eloquaint\Attributes\HasOne $attribute */

        return $this->hasOne(
            $attribute->getRelated(),
            $attribute->foreignKey,
            $attribute->localKey
        );
    }

    /**
     * Resolve a BelongsTo relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return BelongsTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveBelongsTo(RelationshipAttribute $attribute): BelongsTo
    {
        /** @var \StevenFox\Eloquaint\Attributes\BelongsTo $attribute */

        // If no relation is provided, we will guess the relation from the related class.
        if (($relation = $attribute->relation) === null) {
            $relation = Str::camel(class_basename($attribute->getRelated()));
        }

        return $this->belongsTo(
            $attribute->getRelated(),
            $attribute->foreignKey,
            $attribute->ownerKey,
            $relation,
        );
    }

    /**
     * Resolve a BelongsToMany relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return BelongsToMany<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveBelongsToMany(RelationshipAttribute $attribute): BelongsToMany
    {
        /** @var \StevenFox\Eloquaint\Attributes\BelongsToMany $attribute */

        // If no relation is provided, we will guess the relation from the related class.
        if (($relation = $attribute->relation) === null) {
            $relation = Str::camel(Str::plural(class_basename($attribute->getRelated())));
        }

        // It is also possible to get this value by inspecting a debug_backtrace,
        // searching for the call to __call or __get,
        // and pulling the first argument (like 'posts' or 'tags').

        return $this->belongsToMany(
            $attribute->getRelated(),
            $attribute->table,
            $attribute->foreignPivotKey,
            $attribute->relatedPivotKey,
            $attribute->parentKey,
            $attribute->relatedKey,
            $relation,
        );
    }

    /**
     * Resolve a HasManyThrough relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return HasManyThrough<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveHasManyThrough(RelationshipAttribute $attribute): HasManyThrough
    {
        /** @var \StevenFox\Eloquaint\Attributes\HasManyThrough $attribute */
        return $this->hasManyThrough(
            $attribute->getRelated(),
            $attribute->through,
            $attribute->firstKey,
            $attribute->secondKey,
            $attribute->localKey,
            $attribute->secondLocalKey
        );
    }

    /**
     * Resolve a HasOneThrough relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return HasOneThrough<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveHasOneThrough(RelationshipAttribute $attribute): HasOneThrough
    {
        /** @var \StevenFox\Eloquaint\Attributes\HasOneThrough $attribute */
        return $this->hasOneThrough(
            $attribute->getRelated(),
            $attribute->through,
            $attribute->firstKey,
            $attribute->secondKey,
            $attribute->localKey,
            $attribute->secondLocalKey
        );
    }

    /**
     * Resolve a MorphMany relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return MorphMany<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveMorphMany(RelationshipAttribute $attribute): MorphMany
    {
        /** @var \StevenFox\Eloquaint\Attributes\MorphMany $attribute */
        return $this->morphMany(
            $attribute->getRelated(),
            $attribute->name,
            $attribute->type,
            $attribute->id,
            $attribute->localKey
        );
    }

    /**
     * Resolve a MorphOne relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return MorphOne<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveMorphOne(RelationshipAttribute $attribute): MorphOne
    {
        /** @var \StevenFox\Eloquaint\Attributes\MorphOne $attribute */
        return $this->morphOne(
            $attribute->getRelated(),
            $attribute->name,
            $attribute->type,
            $attribute->id,
            $attribute->localKey
        );
    }

    /**
     * Resolve a MorphTo relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveMorphTo(RelationshipAttribute $attribute): MorphTo
    {
        /** @var \StevenFox\Eloquaint\Attributes\MorphTo $attribute */

        // if (($name = $attribute->name) === null) {
        //     $name = Str::camel(class_basename($this));
        // }

        return $this->morphTo(
            $attribute->name,
            $attribute->type,
            $attribute->id,
            $attribute->ownerKey
        );
    }

    /**
     * Resolve a MorphToMany relationship.
     *
     * @param  RelationshipAttribute  $attribute  The relationship attribute
     * @return MorphToMany<\Illuminate\Database\Eloquent\Model, $this>
     */
    protected function resolveMorphToMany(RelationshipAttribute $attribute): MorphToMany
    {
        /** @var \StevenFox\Eloquaint\Attributes\MorphToMany $attribute */
        return $this->morphToMany(
            $attribute->getRelated(),
            $attribute->name,
            $attribute->table,
            $attribute->foreignPivotKey,
            $attribute->relatedPivotKey,
            $attribute->parentKey,
            $attribute->relatedKey,
            $attribute->relation,
            $attribute->inverse
        );
    }
}
