<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Tests\Models\Author;
use StevenFox\Eloquaint\Tests\Models\Comment;
use StevenFox\Eloquaint\Tests\Models\Post;
use StevenFox\Eloquaint\Tests\Models\Tag;

it('can resolve HasMany relationships from attributes', function () {
    $author = new Author;

    // Test that the posts relationship exists and is the correct type
    $postsRelation = $author->posts();
    expect($postsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);

    // Test that the related model is correct
    expect($postsRelation->getRelated())->toBeInstanceOf(Post::class);
});

it('can resolve BelongsTo relationships from attributes', function () {
    $post = new Post;

    // Test that the author relationship exists and is the correct type
    $authorRelation = $post->author();
    expect($authorRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);

    // Test that the related model is correct
    expect($authorRelation->getRelated())->toBeInstanceOf(Author::class);
});

it('can resolve BelongsToMany relationships from attributes', function () {
    $post = new Post;

    // Test that the tags relationship exists and is the correct type
    $tagsRelation = $post->tags();
    expect($tagsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);

    // Test that the related model is correct
    expect($tagsRelation->getRelated())->toBeInstanceOf(Tag::class);
});

it('can resolve custom named relationships with constraints', function () {
    $author = new Author;

    // Test that the publishedPosts relationship exists
    $publishedPostsRelation = $author->publishedPosts();
    expect($publishedPostsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);

    // Test that the related model is correct
    expect($publishedPostsRelation->getRelated())->toBeInstanceOf(Post::class);
});

it('caches relationship definitions', function () {
    // Create multiple instances to test caching
    $author1 = new Author;
    $author2 = new Author;

    // Both should have the same cached relationships
    $relation1 = $author1->posts();
    $relation2 = $author2->posts();

    expect($relation1)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    expect($relation2)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('falls back to parent __call for non-relationship methods', function () {
    $author = new Author;

    // Test that non-relationship methods still work
    expect(function () use ($author) {
        $author->nonExistentMethod();
    })->toThrow(\BadMethodCallException::class);
});

it('resolves relationship names correctly', function () {
    $comment = new Comment;

    // Test that singular relationship names are generated correctly
    $postRelation = $comment->post();
    expect($postRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);

    $tag = new Tag;

    // Test that plural relationship names are generated correctly
    $postsRelation = $tag->posts();
    expect($postsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});
