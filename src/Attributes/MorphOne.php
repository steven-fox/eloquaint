<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;

/**
 * Attribute for defining MorphOne relationships.
 *
 * This attribute can be applied to classes to define
 * a polymorphic one-to-one relationship.
 *
 * @example
 * ```php
 * #[MorphOne(Image::class, name: 'imageable')]
 * class Post extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class MorphOne implements RelationshipAttribute
{
    /**
     * Create a new MorphOne attribute.
     *
     * @param  string  $related  The related model class name
     * @param  string  $name  The morph name
     * @param  string|null  $type  The type column name
     * @param  string|null  $id  The id column name
     * @param  string|null  $localKey  The local key column name
     * @param  string|null  $relationName  Custom name for the relationship
     * @param  array<string, mixed>  $where  Where constraints for the relationship
     */
    public function __construct(
        public string $related,
        public string $name,
        public ?string $type = null,
        public ?string $id = null,
        public ?string $localKey = null,
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
        return 'morphOne';
    }
}
