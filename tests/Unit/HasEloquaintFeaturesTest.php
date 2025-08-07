<?php

declare(strict_types=1);

use StevenFox\Eloquaint\Tests\Models\PostWithScopes;

it('provides unified access to both relationships and scopes', function () {
    $post = new PostWithScopes;

    // Test relationships work
    $author = $post->author();
    expect($author)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);

    $comments = $post->comments();
    expect($comments)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);

    // Test scopes work
    $publishedQuery = $post->published();
    expect($publishedQuery)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

    $popularQuery = $post->popular();
    expect($popularQuery)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

it('handles static scope calls correctly', function () {
    // Test static scope calls
    $publishedQuery = PostWithScopes::published();
    expect($publishedQuery)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

    $popularQuery = PostWithScopes::popular();
    expect($popularQuery)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

it('prioritizes scopes over relationships in method resolution', function () {
    // If there were both a scope and relationship with the same name,
    // the scope should take precedence in __call
    $post = new PostWithScopes;

    // published() should return a query builder (scope), not a relationship
    $result = $post->published();
    expect($result)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

it('falls back to parent methods when no attribute matches', function () {
    $post = new PostWithScopes;

    // Test that non-existent methods still throw proper exceptions
    expect(function () use ($post) {
        $post->nonExistentMethod();
    })->toThrow(\BadMethodCallException::class);

    // Test that static non-existent methods also throw proper exceptions
    expect(function () {
        PostWithScopes::nonExistentStaticMethod();
    })->toThrow(\BadMethodCallException::class);
});

it('can chain scopes with query methods and relationships', function () {
    // This tests that the query builder returned by scopes
    // can be further chained with other methods
    $query = PostWithScopes::published()
        ->with('author')  // Eager load relationship
        ->where('title', 'like', '%test%');

    expect($query)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
    expect($query->toSql())->toContain('where "published" = ?');
    expect($query->toSql())->toContain('and "title" like ?');
});
