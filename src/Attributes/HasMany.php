<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;

/**
 * Attribute for defining HasMany relationships.
 *
 * This attribute can be applied to classes to define
 * a one-to-many relationship with another model.
 *
 * @example
 * ```php
 * #[HasMany(Post::class)]
 * class Author extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 * @example With custom constraints:
 * ```php
 * #[HasMany(Post::class, name: 'publishedPosts', where: ['published' => true])]
 * class Author extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class HasMany implements RelationshipAttribute
{
    /**
     * Create a new HasMany attribute.
     *
     * @param  string  $related  The related model class name
     * @param  string|null  $foreignKey  The foreign key column name
     * @param  string|null  $localKey  The local key column name
     * @param  string|null  $name  Custom name for the relationship
     * @param  array<string, mixed>  $where  Where constraints for the relationship
     */
    public function __construct(
        public string $related,
        public ?string $foreignKey = null,
        public ?string $localKey = null,
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
        return 'hasMany';
    }
}
