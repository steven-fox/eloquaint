<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;

/**
 * Attribute for defining MorphToMany relationships.
 *
 * This attribute can be applied to classes to define
 * a polymorphic many-to-many relationship.
 *
 * @example
 * ```php
 * #[MorphToMany(Tag::class, name: 'taggable')]
 * class Post extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class MorphToMany implements RelationshipAttribute
{
    /**
     * Create a new MorphToMany attribute.
     *
     * @param  string  $related  The related model class name
     * @param  string  $name  The morph name
     * @param  string|null  $table  The pivot table name
     * @param  string|null  $foreignPivotKey  The foreign pivot key column name
     * @param  string|null  $relatedPivotKey  The related pivot key column name
     * @param  string|null  $parentKey  The parent key column name
     * @param  string|null  $relatedKey  The related key column name
     * @param  string|null  $relation  The relation name
     * @param  bool  $inverse  Whether this is the inverse relationship
     * @param  string|null  $relationName  Custom name for the relationship
     * @param  array<string, mixed>  $where  Where constraints for the relationship
     */
    public function __construct(
        public string $related,
        public string $name,
        public ?string $table = null,
        public ?string $foreignPivotKey = null,
        public ?string $relatedPivotKey = null,
        public ?string $parentKey = null,
        public ?string $relatedKey = null,
        public ?string $relation = null,
        public bool $inverse = false,
        public ?string $relationName = null,
        public array $where = [],
    ) {}

    /**
     * Get the related model class name.
     *
     * @return string The fully qualified class name of the related model
     */
    public function getRelated(): string
    {
        return $this->related;
    }

    /**
     * Get the custom name for this relationship.
     *
     * @return string|null The custom relationship name, or null to use auto-generated name
     */
    public function getName(): ?string
    {
        return $this->relationName;
    }

    /**
     * Get the where constraints for this relationship.
     *
     * @return array<string, mixed> Array of column => value constraints
     */
    public function getWhereConstraints(): array
    {
        return $this->where;
    }

    /**
     * Get the relationship type identifier.
     *
     * @return string The relationship type
     */
    public function getRelationshipType(): string
    {
        return 'morphToMany';
    }
}
