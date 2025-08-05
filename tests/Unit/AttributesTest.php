<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\BelongsToMany;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\HasOne;
use StevenFox\Eloquaint\Tests\Models\Author;
use StevenFox\Eloquaint\Tests\Models\Post;

it('HasMany attribute implements RelationshipAttribute interface', function () {
    $attribute = new HasMany(Post::class);

    expect($attribute->getRelated())->toBe(Post::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('hasMany');
});

it('HasMany attribute supports custom name and constraints', function () {
    $attribute = new HasMany(
        related: Post::class,
        name: 'publishedPosts',
        where: ['published' => true]
    );

    expect($attribute->getRelated())->toBe(Post::class);
    expect($attribute->getName())->toBe('publishedPosts');
    expect($attribute->getWhereConstraints())->toBe(['published' => true]);
    expect($attribute->getRelationshipType())->toBe('hasMany');
});

it('HasOne attribute implements RelationshipAttribute interface', function () {
    $attribute = new HasOne(Post::class);

    expect($attribute->getRelated())->toBe(Post::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('hasOne');
});

it('BelongsTo attribute implements RelationshipAttribute interface', function () {
    $attribute = new BelongsTo(Author::class);

    expect($attribute->getRelated())->toBe(Author::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('belongsTo');
});

it('BelongsToMany attribute implements RelationshipAttribute interface', function () {
    $attribute = new BelongsToMany(Post::class);

    expect($attribute->getRelated())->toBe(Post::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('belongsToMany');
});

it('attributes support all constructor parameters', function () {
    $attribute = new HasMany(
        related: Post::class,
        foreignKey: 'author_id',
        localKey: 'id',
        name: 'customPosts',
        where: ['status' => 'active']
    );

    expect($attribute->related)->toBe(Post::class);
    expect($attribute->foreignKey)->toBe('author_id');
    expect($attribute->localKey)->toBe('id');
    expect($attribute->name)->toBe('customPosts');
    expect($attribute->where)->toBe(['status' => 'active']);
});
