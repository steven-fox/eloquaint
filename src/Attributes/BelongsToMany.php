<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;

/**
 * Attribute for defining BelongsToMany relationships.
 *
 * This attribute can be applied to classes or properties to define
 * a many-to-many relationship with another model.
 *
 * @example
 * ```php
 * #[BelongsToMany(Tag::class)]
 * class Post extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 * @example With custom pivot table:
 * ```php
 * #[BelongsToMany(Tag::class, table: 'post_tags')]
 * class Post extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final readonly class BelongsToMany implements RelationshipAttribute
{
    /**
     * Create a new BelongsToMany attribute.
     *
     * @param  string  $related  The related model class name
     * @param  string|null  $table  The pivot table name
     * @param  string|null  $foreignPivotKey  The foreign pivot key column name
     * @param  string|null  $relatedPivotKey  The related pivot key column name
     * @param  string|null  $parentKey  The parent key column name
     * @param  string|null  $relatedKey  The related key column name
     * @param  string|null  $relation  The relation name
     * @param  string|null  $name  Custom name for the relationship
     * @param  array<string, mixed>  $where  Where constraints for the relationship
     */
    public function __construct(
        public string $related,
        public ?string $table = null,
        public ?string $foreignPivotKey = null,
        public ?string $relatedPivotKey = null,
        public ?string $parentKey = null,
        public ?string $relatedKey = null,
        public ?string $relation = null,
        public ?string $name = null,
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
        return $this->name;
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
        return 'belongsToMany';
    }
}
