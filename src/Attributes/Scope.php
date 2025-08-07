<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Attributes;

use Attribute;

/**
 * Attribute for defining local scopes.
 *
 * This attribute can be applied to classes to define
 * local scopes using PHP attributes instead of traditional methods.
 *
 * @example Simple scopes (2 args - column and value):
 * ```php
 * #[Scope('published', 'published', true)]
 * #[Scope('active', 'status', 'active')]
 * class Post extends Model
 * {
 *     use HasEloquaintFeatures;
 * }
 * ```
 * @example Simple scopes (3 args - column, operator, value):
 * ```php
 * #[Scope('popular', 'views', '>', 1000)]
 * #[Scope('old', 'created_at', '<', '2024-01-01')]
 * class Post extends Model
 * {
 *     use HasEloquaintFeatures;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class Scope
{
    /**
     * Create a new Scope attribute.
     *
     * @param  string  $name  The scope name
     * @param  string  $column  The column name for where clauses
     * @param  mixed  $operatorOrValue  The operator (if 3 args) or value (if 2 args)
     * @param  mixed  $value  The value (when operator is specified)
     */
    public function __construct(
        public string $name,
        public string $column,
        public mixed $operatorOrValue = null,
        public mixed $value = null,
    ) {}

    /**
     * Get the scope name.
     *
     * @return string The scope name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the column name for where clauses.
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Get the operator for where clauses.
     */
    public function getOperator(): string
    {
        // If value is null, then operatorOrValue is the value and operator is '='
        if ($this->value === null) {
            return '=';
        }

        // If value is not null, then operatorOrValue is the operator
        return (string) $this->operatorOrValue;
    }

    /**
     * Get the value for where clauses.
     */
    public function getValue(): mixed
    {
        // If value is null, then operatorOrValue is the value
        if ($this->value === null) {
            return $this->operatorOrValue;
        }

        // If value is not null, then value is the value
        return $this->value;
    }
}
