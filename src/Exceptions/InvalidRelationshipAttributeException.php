<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Exceptions;

use Exception;

/**
 * Exception thrown when an invalid relationship attribute is encountered.
 *
 * This exception is used to indicate problems with relationship attribute
 * configuration, such as missing required parameters or invalid class names.
 */
final class InvalidRelationshipAttributeException extends Exception
{
    /**
     * Create a new exception for an invalid related class.
     *
     * @param  string  $className  The invalid class name
     * @param  string  $relationshipType  The relationship type
     */
    public static function invalidRelatedClass(string $className, string $relationshipType): self
    {
        return new self(
            "Invalid related class '{$className}' for {$relationshipType} relationship. ".
            'Class must exist and extend Illuminate\Database\Eloquent\Model.'
        );
    }

    /**
     * Create a new exception for missing required parameters.
     *
     * @param  string  $parameter  The missing parameter name
     * @param  string  $relationshipType  The relationship type
     */
    public static function missingRequiredParameter(string $parameter, string $relationshipType): self
    {
        return new self(
            "Missing required parameter '{$parameter}' for {$relationshipType} relationship."
        );
    }

    /**
     * Create a new exception for invalid where constraints.
     *
     * @param  string  $relationshipType  The relationship type
     */
    public static function invalidWhereConstraints(string $relationshipType): self
    {
        return new self(
            "Invalid where constraints for {$relationshipType} relationship. ".
            'Constraints must be an array of column => value pairs.'
        );
    }
}
