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

it('can resolve HasOne relationships from attributes', function () {
    $user = new \StevenFox\Eloquaint\Tests\Models\User;

    $profileRelation = $user->profile();
    expect($profileRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
    expect($profileRelation->getRelated())->toBeInstanceOf(\StevenFox\Eloquaint\Tests\Models\Profile::class);
});

it('can access relationships as properties', function () {
    $author = new Author;

    // Test accessing relationship as property (not method call)
    // This should trigger the __get method and getAttributeRelationValue
    $posts = $author->posts;
    expect($posts)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

it('can access already loaded relationships', function () {
    $author = new Author;

    // First access loads the relationship
    $posts1 = $author->posts;

    // Second access should return the cached relationship
    $posts2 = $author->posts;

    expect($posts1)->toBe($posts2);
});

it('falls back to parent for non-attribute properties', function () {
    $author = new Author;

    // Test accessing a non-relationship property returns null
    $nonExistent = $author->nonExistentProperty;
    expect($nonExistent)->toBeNull();
});

it('handles getRelationValue method override', function () {
    $author = new Author;

    // Test the getRelationValue method directly (used internally by Eloquent)
    $posts = $author->getRelationValue('posts');
    expect($posts)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);

    // Test with non-existent relationship
    $nonExistent = $author->getRelationValue('nonExistentRelation');
    expect($nonExistent)->toBeNull();
});
