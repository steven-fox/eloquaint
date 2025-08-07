<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Attributes\Scope;
use StevenFox\Eloquaint\Tests\Models\PostWithScopes;

it('resolves attribute scopes during model boot', function () {
    $post = new PostWithScopes;

    // Check that scopes are cached
    expect($post->isAttributeScope('published'))->toBeTrue();
    expect($post->isAttributeScope('draft'))->toBeTrue();
    expect($post->isAttributeScope('popular'))->toBeTrue();
    expect($post->isAttributeScope('nonExistent'))->toBeFalse();
});

it('can call simple attribute scopes as instance methods', function () {
    $post = new PostWithScopes;

    $query = $post->published();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->toSql())->toContain('where "published" = ?');
});

it('can call simple attribute scopes as static methods', function () {
    $query = PostWithScopes::published();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->toSql())->toContain('where "published" = ?');
});

it('can call simple scopes with operators', function () {
    $post = new PostWithScopes;

    $query = $post->popular();

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->toSql())->toContain('where "views" > ?');
});



it('can chain attribute scopes with regular query methods', function () {
    $query = PostWithScopes::published()->where('title', 'like', '%test%');

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->toSql())->toContain('where "published" = ?');
    expect($query->toSql())->toContain('and "title" like ?');
});

it('can apply multiple scopes by calling them separately', function () {
    // Test that we can apply multiple scopes by calling them on the model
    $publishedQuery = PostWithScopes::published();
    $popularQuery = PostWithScopes::popular();

    expect($publishedQuery->toSql())->toContain('where "published" = ?');
    expect($popularQuery->toSql())->toContain('where "views" > ?');

    // Test combining with regular query methods
    $complexQuery = PostWithScopes::published()
        ->where('title', 'like', '%test%')
        ->orderBy('created_at', 'desc');

    expect($complexQuery->toSql())->toContain('where "published" = ?');
    expect($complexQuery->toSql())->toContain('and "title" like ?');
    expect($complexQuery->toSql())->toContain('order by "created_at" desc');
});

it('falls back to parent __call for non-scope methods', function () {
    $post = new PostWithScopes;

    // Test that non-scope methods still work (should throw BadMethodCallException)
    expect(function () use ($post) {
        $post->nonExistentMethod();
    })->toThrow(\BadMethodCallException::class);
});

it('caches scope definitions for performance', function () {
    // Create multiple instances to test caching
    $post1 = new PostWithScopes;
    $post2 = new PostWithScopes;

    // Both should have the same cached scopes
    expect($post1->isAttributeScope('published'))->toBeTrue();
    expect($post2->isAttributeScope('published'))->toBeTrue();

    $query1 = $post1->published();
    $query2 = $post2->published();

    expect($query1)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query2)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

it('works alongside attribute relationships', function () {
    $post = new PostWithScopes;

    // Test that both scopes and relationships work
    expect($post->isAttributeScope('published'))->toBeTrue();

    // This should work (relationship)
    $author = $post->author();
    expect($author)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);

    // This should also work (scope)
    $query = $post->published();
    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});
