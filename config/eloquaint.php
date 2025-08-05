<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Eloquaint Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the Eloquaint package.
    | You can customize various aspects of how attribute relationships
    | are processed and cached.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Cache Relationships
    |--------------------------------------------------------------------------
    |
    | When enabled, relationship definitions will be cached to improve
    | performance. This is recommended for production environments.
    |
    */
    'cache_relationships' => env('ELOQUAINT_CACHE_RELATIONSHIPS', true),

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, Eloquaint will throw exceptions for invalid relationship
    | configurations. When disabled, invalid relationships will be ignored.
    |
    */
    'strict_mode' => env('ELOQUAINT_STRICT_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Auto-discover Relationships
    |--------------------------------------------------------------------------
    |
    | When enabled, Eloquaint will automatically discover and register
    | relationships defined via attributes. Disable this if you want to
    | manually control relationship registration.
    |
    */
    'auto_discover' => env('ELOQUAINT_AUTO_DISCOVER', true),
];
