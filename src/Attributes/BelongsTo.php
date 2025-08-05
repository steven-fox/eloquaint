<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;

/**
 * Attribute for defining BelongsTo relationships.
 *
 * This attribute can be applied to classes to define
 * an inverse one-to-one or one-to-many relationship.
 *
 * @example
 * ```php
 * #[BelongsTo(Author::class)]
 * class Post extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 * @example With custom keys:
 * ```php
 * #[BelongsTo(Author::class, foreignKey: 'author_id', ownerKey: 'id')]
 * class Post extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class BelongsTo implements RelationshipAttribute
{
    /**
     * Create a new BelongsTo attribute.
     *
     * @param  string  $related  The related model class name
     * @param  string|null  $foreignKey  The foreign key column name
     * @param  string|null  $ownerKey  The owner key column name
     * @param  string|null  $relation  The relation name
     * @param  string|null  $name  Custom name for the relationship
     * @param  array<string, mixed>  $where  Where constraints for the relationship
     */
    public function __construct(
        public string $related,
        public ?string $foreignKey = null,
        public ?string $ownerKey = null,
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
        return 'belongsTo';
    }
}
