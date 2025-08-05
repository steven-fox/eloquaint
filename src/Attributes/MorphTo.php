<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;

/**
 * Attribute for defining MorphTo relationships.
 *
 * This attribute can be applied to classes or properties to define
 * the inverse of a polymorphic relationship.
 *
 * @example
 * ```php
 * #[MorphTo(name: 'commentable')]
 * class Comment extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final readonly class MorphTo implements RelationshipAttribute
{
    /**
     * Create a new MorphTo attribute.
     *
     * @param  string|null  $name  The morph name
     * @param  string|null  $type  The type column name
     * @param  string|null  $id  The id column name
     * @param  string|null  $ownerKey  The owner key column name
     * @param  string|null  $relationName  Custom name for the relationship
     * @param  array<string, mixed>  $where  Where constraints for the relationship
     */
    public function __construct(
        public ?string $name = null,
        public ?string $type = null,
        public ?string $id = null,
        public ?string $ownerKey = null,
        public ?string $relationName = null,
        public array $where = [],
    ) {}

    /**
     * Get the related model class name.
     *
     * Note: MorphTo relationships don't have a fixed related class.
     *
     * @return string Empty string as MorphTo doesn't have a fixed related class
     */
    public function getRelated(): string
    {
        return '';
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
        return 'morphTo';
    }
}
