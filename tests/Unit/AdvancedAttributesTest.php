<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Attributes\HasManyThrough;
use StevenFox\Eloquaint\Attributes\HasOneThrough;
use StevenFox\Eloquaint\Attributes\MorphMany;
use StevenFox\Eloquaint\Attributes\MorphOne;
use StevenFox\Eloquaint\Attributes\MorphTo;
use StevenFox\Eloquaint\Attributes\MorphToMany;
use StevenFox\Eloquaint\Tests\Models\Comment;
use StevenFox\Eloquaint\Tests\Models\Image;
use StevenFox\Eloquaint\Tests\Models\Post;
use StevenFox\Eloquaint\Tests\Models\Profile;
use StevenFox\Eloquaint\Tests\Models\Tag;
use StevenFox\Eloquaint\Tests\Models\User;
use StevenFox\Eloquaint\Tests\Models\Video;

it('HasManyThrough attribute implements RelationshipAttribute interface', function () {
    $attribute = new HasManyThrough(Comment::class, through: Post::class);

    expect($attribute->getRelated())->toBe(Comment::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('hasManyThrough');
    expect($attribute->through)->toBe(Post::class);
    expect($attribute->firstKey)->toBeNull();
    expect($attribute->secondKey)->toBeNull();
    expect($attribute->localKey)->toBeNull();
    expect($attribute->secondLocalKey)->toBeNull();
});

it('HasManyThrough attribute supports custom parameters', function () {
    $attribute = new HasManyThrough(
        related: Comment::class,
        through: Post::class,
        firstKey: 'user_id',
        secondKey: 'post_id',
        localKey: 'id',
        secondLocalKey: 'id',
        name: 'customComments',
        where: ['active' => true]
    );

    expect($attribute->getRelated())->toBe(Comment::class);
    expect($attribute->getName())->toBe('customComments');
    expect($attribute->getWhereConstraints())->toBe(['active' => true]);
    expect($attribute->through)->toBe(Post::class);
    expect($attribute->firstKey)->toBe('user_id');
    expect($attribute->secondKey)->toBe('post_id');
    expect($attribute->localKey)->toBe('id');
    expect($attribute->secondLocalKey)->toBe('id');
});

it('HasOneThrough attribute implements RelationshipAttribute interface', function () {
    $attribute = new HasOneThrough(Profile::class, through: User::class);

    expect($attribute->getRelated())->toBe(Profile::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('hasOneThrough');
    expect($attribute->through)->toBe(User::class);
    expect($attribute->firstKey)->toBeNull();
    expect($attribute->secondKey)->toBeNull();
    expect($attribute->localKey)->toBeNull();
    expect($attribute->secondLocalKey)->toBeNull();
});

it('HasOneThrough attribute supports custom parameters', function () {
    $attribute = new HasOneThrough(
        related: Profile::class,
        through: User::class,
        firstKey: 'country_id',
        secondKey: 'user_id',
        localKey: 'id',
        secondLocalKey: 'id',
        name: 'customProfile',
        where: ['active' => true]
    );

    expect($attribute->getRelated())->toBe(Profile::class);
    expect($attribute->getName())->toBe('customProfile');
    expect($attribute->getWhereConstraints())->toBe(['active' => true]);
    expect($attribute->through)->toBe(User::class);
    expect($attribute->firstKey)->toBe('country_id');
    expect($attribute->secondKey)->toBe('user_id');
    expect($attribute->localKey)->toBe('id');
    expect($attribute->secondLocalKey)->toBe('id');
});

it('MorphMany attribute implements RelationshipAttribute interface', function () {
    $attribute = new MorphMany(Image::class, name: 'imageable');

    expect($attribute->getRelated())->toBe(Image::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('morphMany');
    expect($attribute->name)->toBe('imageable');
    expect($attribute->type)->toBeNull();
    expect($attribute->id)->toBeNull();
    expect($attribute->localKey)->toBeNull();
    expect($attribute->relationName)->toBeNull();
});

it('MorphMany attribute supports custom parameters', function () {
    $attribute = new MorphMany(
        related: Image::class,
        name: 'imageable',
        type: 'imageable_type',
        id: 'imageable_id',
        localKey: 'id',
        relationName: 'customImages',
        where: ['active' => true]
    );

    expect($attribute->getRelated())->toBe(Image::class);
    expect($attribute->getName())->toBe('customImages');
    expect($attribute->getWhereConstraints())->toBe(['active' => true]);
    expect($attribute->name)->toBe('imageable');
    expect($attribute->type)->toBe('imageable_type');
    expect($attribute->id)->toBe('imageable_id');
    expect($attribute->localKey)->toBe('id');
    expect($attribute->relationName)->toBe('customImages');
});

it('MorphOne attribute implements RelationshipAttribute interface', function () {
    $attribute = new MorphOne(Video::class, name: 'videoable');

    expect($attribute->getRelated())->toBe(Video::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('morphOne');
    expect($attribute->name)->toBe('videoable');
    expect($attribute->type)->toBeNull();
    expect($attribute->id)->toBeNull();
    expect($attribute->localKey)->toBeNull();
    expect($attribute->relationName)->toBeNull();
});

it('MorphOne attribute supports custom parameters', function () {
    $attribute = new MorphOne(
        related: Video::class,
        name: 'videoable',
        type: 'videoable_type',
        id: 'videoable_id',
        localKey: 'id',
        relationName: 'customVideo',
        where: ['active' => true]
    );

    expect($attribute->getRelated())->toBe(Video::class);
    expect($attribute->getName())->toBe('customVideo');
    expect($attribute->getWhereConstraints())->toBe(['active' => true]);
    expect($attribute->name)->toBe('videoable');
    expect($attribute->type)->toBe('videoable_type');
    expect($attribute->id)->toBe('videoable_id');
    expect($attribute->localKey)->toBe('id');
    expect($attribute->relationName)->toBe('customVideo');
});

it('MorphTo attribute implements RelationshipAttribute interface', function () {
    $attribute = new MorphTo(name: 'imageable');

    expect($attribute->getRelated())->toBe('');
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('morphTo');
    expect($attribute->name)->toBe('imageable');
    expect($attribute->type)->toBeNull();
    expect($attribute->id)->toBeNull();
    expect($attribute->ownerKey)->toBeNull();
    expect($attribute->relationName)->toBeNull();
});

it('MorphTo attribute supports custom parameters', function () {
    $attribute = new MorphTo(
        name: 'imageable',
        type: 'imageable_type',
        id: 'imageable_id',
        ownerKey: 'id',
        relationName: 'customImageable',
        where: ['active' => true]
    );

    expect($attribute->getRelated())->toBe('');
    expect($attribute->getName())->toBe('customImageable');
    expect($attribute->getWhereConstraints())->toBe(['active' => true]);
    expect($attribute->name)->toBe('imageable');
    expect($attribute->type)->toBe('imageable_type');
    expect($attribute->id)->toBe('imageable_id');
    expect($attribute->ownerKey)->toBe('id');
    expect($attribute->relationName)->toBe('customImageable');
});

it('MorphToMany attribute implements RelationshipAttribute interface', function () {
    $attribute = new MorphToMany(Tag::class, name: 'taggable');

    expect($attribute->getRelated())->toBe(Tag::class);
    expect($attribute->getName())->toBeNull();
    expect($attribute->getWhereConstraints())->toBe([]);
    expect($attribute->getRelationshipType())->toBe('morphToMany');
    expect($attribute->name)->toBe('taggable');
    expect($attribute->table)->toBeNull();
    expect($attribute->foreignPivotKey)->toBeNull();
    expect($attribute->relatedPivotKey)->toBeNull();
    expect($attribute->parentKey)->toBeNull();
    expect($attribute->relatedKey)->toBeNull();
    expect($attribute->relation)->toBeNull();
    expect($attribute->inverse)->toBeFalse();
    expect($attribute->relationName)->toBeNull();
});

it('MorphToMany attribute supports custom parameters', function () {
    $attribute = new MorphToMany(
        related: Tag::class,
        name: 'taggable',
        table: 'taggables',
        foreignPivotKey: 'taggable_id',
        relatedPivotKey: 'tag_id',
        parentKey: 'id',
        relatedKey: 'id',
        relation: 'tags',
        inverse: true,
        relationName: 'customTags',
        where: ['active' => true]
    );

    expect($attribute->getRelated())->toBe(Tag::class);
    expect($attribute->getName())->toBe('customTags');
    expect($attribute->getWhereConstraints())->toBe(['active' => true]);
    expect($attribute->name)->toBe('taggable');
    expect($attribute->table)->toBe('taggables');
    expect($attribute->foreignPivotKey)->toBe('taggable_id');
    expect($attribute->relatedPivotKey)->toBe('tag_id');
    expect($attribute->parentKey)->toBe('id');
    expect($attribute->relatedKey)->toBe('id');
    expect($attribute->relation)->toBe('tags');
    expect($attribute->inverse)->toBeTrue();
    expect($attribute->relationName)->toBe('customTags');
});
