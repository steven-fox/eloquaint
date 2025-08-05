<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Exceptions\InvalidRelationshipAttributeException;

it('can create exception for invalid related class', function () {
    $exception = InvalidRelationshipAttributeException::invalidRelatedClass('NonExistentClass', 'hasMany');

    expect($exception)->toBeInstanceOf(InvalidRelationshipAttributeException::class);
    expect($exception->getMessage())->toBe(
        "Invalid related class 'NonExistentClass' for hasMany relationship. ".
        'Class must exist and extend Illuminate\Database\Eloquent\Model.'
    );
});

it('can create exception for missing required parameter', function () {
    $exception = InvalidRelationshipAttributeException::missingRequiredParameter('related', 'belongsTo');

    expect($exception)->toBeInstanceOf(InvalidRelationshipAttributeException::class);
    expect($exception->getMessage())->toBe(
        "Missing required parameter 'related' for belongsTo relationship."
    );
});

it('can create exception for invalid where constraints', function () {
    $exception = InvalidRelationshipAttributeException::invalidWhereConstraints('morphMany');

    expect($exception)->toBeInstanceOf(InvalidRelationshipAttributeException::class);
    expect($exception->getMessage())->toBe(
        'Invalid where constraints for morphMany relationship. '.
        'Constraints must be an array of column => value pairs.'
    );
});

it('extends Exception class', function () {
    $exception = new InvalidRelationshipAttributeException('Test message');

    expect($exception)->toBeInstanceOf(Exception::class);
    expect($exception->getMessage())->toBe('Test message');
});
