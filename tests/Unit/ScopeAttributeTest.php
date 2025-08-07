<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Attributes\Scope;

it('can create simple scope with 2 args (column and value)', function () {
    $scope = new Scope('published', 'published', true);

    expect($scope->getName())->toBe('published');
    expect($scope->getColumn())->toBe('published');
    expect($scope->getOperator())->toBe('=');
    expect($scope->getValue())->toBe(true);
});

it('can create simple scope with 3 args (column, operator, value)', function () {
    $scope = new Scope('popular', 'views', '>', 1000);

    expect($scope->getName())->toBe('popular');
    expect($scope->getColumn())->toBe('views');
    expect($scope->getOperator())->toBe('>');
    expect($scope->getValue())->toBe(1000);
});
