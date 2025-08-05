<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Exceptions\InvalidRelationshipAttributeException;
use StevenFox\Eloquaint\Tests\Models\Country;
use StevenFox\Eloquaint\Tests\Models\Image;
use StevenFox\Eloquaint\Tests\Models\Post;
use StevenFox\Eloquaint\Tests\Models\Profile;
use StevenFox\Eloquaint\Tests\Models\PropertyAttributeModel;
use StevenFox\Eloquaint\Tests\Models\Tag;
use StevenFox\Eloquaint\Tests\Models\User;
use StevenFox\Eloquaint\Tests\Models\Video;

it('can resolve HasManyThrough relationships from attributes', function () {
    $country = new Country;

    // The relationship should be named 'posts' (plural of Post)
    $postsRelation = $country->posts();
    expect($postsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasManyThrough::class);
    expect($postsRelation->getRelated())->toBeInstanceOf(Post::class);
});

it('can resolve HasOneThrough relationships from attributes', function () {
    $country = new Country;

    $profileRelation = $country->profile();
    expect($profileRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOneThrough::class);
    expect($profileRelation->getRelated())->toBeInstanceOf(Profile::class);
});

it('can resolve custom named HasManyThrough relationships', function () {
    $country = new Country;

    $customPostsRelation = $country->customPosts();
    expect($customPostsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasManyThrough::class);
    expect($customPostsRelation->getRelated())->toBeInstanceOf(Post::class);
});

it('can resolve MorphMany relationships from attributes', function () {
    $user = new User;

    $imagesRelation = $user->images();
    expect($imagesRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class);
    expect($imagesRelation->getRelated())->toBeInstanceOf(Image::class);
});

it('can resolve MorphOne relationships from attributes', function () {
    $user = new User;

    $videoRelation = $user->video();
    expect($videoRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphOne::class);
    expect($videoRelation->getRelated())->toBeInstanceOf(Video::class);
});

it('can resolve MorphTo relationships from attributes', function () {
    $image = new Image;

    $imageableRelation = $image->imageable();
    expect($imageableRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class);
});

it('can resolve custom named MorphTo relationships', function () {
    $image = new Image;

    $customRelation = $image->customRelation();
    expect($customRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class);
});

it('can resolve MorphToMany relationships from attributes', function () {
    $user = new User;

    $tagsRelation = $user->tags();
    expect($tagsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphToMany::class);
    expect($tagsRelation->getRelated())->toBeInstanceOf(Tag::class);
});

it('can resolve custom named MorphMany relationships with constraints', function () {
    $user = new User;

    $customImagesRelation = $user->customImages();
    expect($customImagesRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class);
    expect($customImagesRelation->getRelated())->toBeInstanceOf(Image::class);
});

it('can resolve property-level attribute relationships', function () {
    $model = new PropertyAttributeModel;

    $postsRelation = $model->posts();
    expect($postsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    expect($postsRelation->getRelated())->toBeInstanceOf(Post::class);

    $authorRelation = $model->author();
    expect($authorRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);

    $imagesRelation = $model->images();
    expect($imagesRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class);
    expect($imagesRelation->getRelated())->toBeInstanceOf(Image::class);
});

it('throws exception for unsupported relationship type', function () {
    $model = new class extends \Illuminate\Database\Eloquent\Model {
        use \StevenFox\Eloquaint\Traits\HasAttributeRelations;

        public function testUnsupportedRelation()
        {
            $attribute = new class implements \StevenFox\Eloquaint\Attributes\Contracts\RelationshipAttribute {
                public function getRelated(): string { return 'TestModel'; }
                public function getName(): ?string { return null; }
                public function getWhereConstraints(): array { return []; }
                public function getRelationshipType(): string { return 'unsupportedType'; }
            };

            return $this->resolveAttributeRelation($attribute);
        }
    };

    expect(fn() => $model->testUnsupportedRelation())
        ->toThrow(InvalidRelationshipAttributeException::class, 'Unsupported relationship type: unsupportedType');
});

it('applies where constraints to relationships', function () {
    $user = new User;

    $customImagesRelation = $user->customImages();
    expect($customImagesRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class);

    // The where constraint should be applied to the query
    $query = $customImagesRelation->getQuery();
    $wheres = $query->getQuery()->wheres;

    // Find the where clause for 'active' column
    $activeWhere = collect($wheres)->first(fn($where) => isset($where['column']) && $where['column'] === 'active');

    expect($activeWhere)->not->toBeNull();
    expect($activeWhere['value'])->toBe(true);
});
