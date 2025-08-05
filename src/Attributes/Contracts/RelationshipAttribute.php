<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes\Contracts;

/**
 * Contract for all relationship attributes.
 *
 * This interface defines the common structure that all relationship
 * attributes must implement to be recognized by the HasAttributeRelations trait.
 */
interface RelationshipAttribute
{
    /**
     * Get the related model class name.
     *
     * @return string The fully qualified class name of the related model
     */
    public function getRelated(): string;

    /**
     * Get the custom name for this relationship.
     *
     * @return string|null The custom relationship name, or null to use auto-generated name
     */
    public function getName(): ?string;

    /**
     * Get the where constraints for this relationship.
     *
     * @return array<string, mixed> Array of column => value constraints
     */
    public function getWhereConstraints(): array;

    /**
     * Get the relationship type identifier.
     *
     * @return string The relationship type (e.g., 'hasMany', 'belongsTo', etc.)
     */
    public function getRelationshipType(): string;
}
