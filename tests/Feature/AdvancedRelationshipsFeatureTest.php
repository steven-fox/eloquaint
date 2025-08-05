<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use StevenFox\Eloquaint\Tests\Models\Country;
use StevenFox\Eloquaint\Tests\Models\Image;
use StevenFox\Eloquaint\Tests\Models\Post;
use StevenFox\Eloquaint\Tests\Models\Profile;
use StevenFox\Eloquaint\Tests\Models\PropertyAttributeModel;
use StevenFox\Eloquaint\Tests\Models\Tag;
use StevenFox\Eloquaint\Tests\Models\User;
use StevenFox\Eloquaint\Tests\Models\Video;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create database tables for testing
    Schema::create('countries', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('code');
        $table->timestamps();
    });

    Schema::create('authors', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->timestamps();
    });

    Schema::create('users', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->unsignedBigInteger('country_id')->nullable();
        $table->timestamps();
    });

    Schema::create('profiles', function ($table) {
        $table->id();
        $table->text('bio')->nullable();
        $table->string('avatar')->nullable();
        $table->foreignId('user_id')->constrained();
        $table->timestamps();
    });

    Schema::create('posts', function ($table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->unsignedBigInteger('author_id');
        $table->unsignedBigInteger('user_id');
        $table->boolean('published')->default(false);
        $table->timestamps();
    });

    Schema::create('comments', function ($table) {
        $table->id();
        $table->text('content');
        $table->unsignedBigInteger('post_id');
        $table->unsignedBigInteger('user_id')->nullable();
        $table->unsignedBigInteger('author_without_relation_id')->nullable();
        $table->morphs('commentable');
        $table->morphs('morphable');
        $table->timestamps();
    });

    Schema::create('tags', function ($table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    Schema::create('post_tag', function ($table) {
        $table->id();
        $table->foreignId('post_id')->constrained();
        $table->foreignId('tag_id')->constrained();
        $table->timestamps();
    });

    Schema::create('images', function ($table) {
        $table->id();
        $table->string('url');
        $table->morphs('imageable');
        $table->boolean('active')->default(true);
        $table->timestamps();
    });

    Schema::create('videos', function ($table) {
        $table->id();
        $table->string('url');
        $table->morphs('videoable');
        $table->timestamps();
    });

    Schema::create('property_attribute_models', function ($table) {
        $table->id();
        $table->string('name');
        $table->foreignId('author_id')->constrained('authors');
        $table->timestamps();
    });

    Schema::create('taggables', function ($table) {
        $table->id();
        $table->morphs('taggable');
        $table->foreignId('tag_id')->constrained();
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
});

it('can create and use HasManyThrough relationships defined with attributes', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);
    $post = Post::create(['title' => 'Test Post', 'content' => 'Content', 'author_id' => $user->id, 'user_id' => $user->id, 'published' => true]);

    $posts = $country->posts;
    expect($posts)->toHaveCount(1);
    expect($posts->first()->title)->toBe('Test Post');
});

it('can create and use HasOneThrough relationships defined with attributes', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);
    $profile = Profile::create(['bio' => 'Test bio', 'user_id' => $user->id]);

    $countryProfile = $country->profile;
    expect($countryProfile)->not->toBeNull();
    expect($countryProfile->bio)->toBe('Test bio');
});

it('can create and use MorphMany relationships defined with attributes', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);

    $image1 = new Image(['url' => 'image1.jpg', 'active' => true]);
    $image2 = new Image(['url' => 'image2.jpg', 'active' => false]);

    $user->images()->save($image1);
    $user->images()->save($image2);

    $images = $user->images;
    expect($images)->toHaveCount(2);
    expect($images->pluck('url')->toArray())->toContain('image1.jpg', 'image2.jpg');
});

it('can create and use MorphOne relationships defined with attributes', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);

    $video = new Video(['url' => 'video1.mp4']);
    $user->video()->save($video);

    $userVideo = $user->video;
    expect($userVideo)->not->toBeNull();
    expect($userVideo->url)->toBe('video1.mp4');
});

it('can create and use MorphTo relationships defined with attributes', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);

    $image = new Image(['url' => 'image1.jpg', 'active' => true]);
    $user->images()->save($image);

    $imageable = $image->imageable;
    expect($imageable)->not->toBeNull();
    expect($imageable->name)->toBe('John');
});

it('can create and use MorphToMany relationships defined with attributes', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);
    $tag1 = Tag::create(['name' => 'PHP']);
    $tag2 = Tag::create(['name' => 'Laravel']);

    $user->tags()->attach([$tag1->id, $tag2->id]);

    $tags = $user->tags;
    expect($tags)->toHaveCount(2);
    expect($tags->pluck('name')->toArray())->toContain('PHP', 'Laravel');
});

it('supports property-level attribute relationships', function () {
    $author = \StevenFox\Eloquaint\Tests\Models\Author::create(['name' => 'Test Author', 'email' => 'author@example.com']);
    $model = PropertyAttributeModel::create(['name' => 'Test Model', 'author_id' => $author->id]);

    // Test that the relationships are properly defined
    expect($model->posts())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    expect($model->author())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
    expect($model->images())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class);
});

it('applies where constraints to morphMany relationships', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);

    $activeImage = new Image(['url' => 'active.jpg', 'active' => true]);
    $inactiveImage = new Image(['url' => 'inactive.jpg', 'active' => false]);

    $user->images()->save($activeImage);
    $user->images()->save($inactiveImage);

    // Verify all images were saved
    expect($user->images)->toHaveCount(2);

    // The customImages relationship should only return active images
    $customImages = $user->customImages;
    expect($customImages)->toHaveCount(1);
    expect($customImages->first()->url)->toBe('active.jpg');
});

it('can query through relationships using Eloquent methods', function () {
    $country = Country::create(['name' => 'USA', 'code' => 'US']);
    $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'country_id' => $country->id]);
    $post1 = Post::create(['title' => 'Published Post', 'content' => 'Content', 'author_id' => $user->id, 'user_id' => $user->id, 'published' => true]);
    $post2 = Post::create(['title' => 'Draft Post', 'content' => 'Content', 'author_id' => $user->id, 'user_id' => $user->id, 'published' => false]);

    $publishedPosts = $country->posts()->where('published', true)->get();
    expect($publishedPosts)->toHaveCount(1);
    expect($publishedPosts->first()->title)->toBe('Published Post');
});
