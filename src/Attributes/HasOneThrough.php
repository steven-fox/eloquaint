<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;
use StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute;

/**
 * Attribute for defining HasOneThrough relationships.
 *
 * This attribute can be applied to classes to define
 * a has-one-through relationship with another model.
 *
 * @example
 * ```php
 * #[HasOneThrough(Profile::class, through: User::class)]
 * class Company extends Model
 * {
 *     use HasAttributeRelations;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class HasOneThrough implements RelationshipAttribute
{
    /**
     * Create a new HasOneThrough attribute.
     *
     * @param  string  $related  The related model class name
     * @param  string  $through  The intermediate model class name
     * @param  string|null  $firstKey  The first key column name
     * @param  string|null  $secondKey  The second key column name
     * @param  string|null  $localKey  The local key column name
     * @param  string|null  $secondLocalKey  The second local key column name
     * @param  string|null  $name  Custom name for the relationship
     * @param  array<string, mixed>  $where  Where constraints for the relationship
     */
    public function __construct(
        public string $related,
        public string $through,
        public ?string $firstKey = null,
        public ?string $secondKey = null,
        public ?string $localKey = null,
        public ?string $secondLocalKey = null,
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
        return 'hasOneThrough';
    }
}
