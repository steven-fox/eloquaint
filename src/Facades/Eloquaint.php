<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Eloquaint package.
 *
 * @see \StevenFox\Eloquaint\Eloquaint
 */
final class Eloquaint extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \StevenFox\Eloquaint\Eloquaint::class;
    }
}
