<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use StevenFox\Eloquaint\Tests\Models\Author;
use StevenFox\Eloquaint\Tests\Models\Comment;
use StevenFox\Eloquaint\Tests\Models\Post;
use StevenFox\Eloquaint\Tests\Models\Tag;

beforeEach(function () {
    // Create test database tables
    Schema::create('authors', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->timestamps();
    });

    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('author_id')->constrained();
        $table->string('title');
        $table->text('content');
        $table->boolean('published')->default(false);
        $table->timestamps();
    });

    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('post_id')->constrained();
        $table->text('content');
        $table->timestamps();
    });

    Schema::create('tags', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    Schema::create('post_tag', function (Blueprint $table) {
        $table->id();
        $table->foreignId('post_id')->constrained();
        $table->foreignId('tag_id')->constrained();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('post_tag');
    Schema::dropIfExists('comments');
    Schema::dropIfExists('tags');
    Schema::dropIfExists('posts');
    Schema::dropIfExists('authors');
});

it('can create and use HasMany relationships defined with attributes', function () {
    // Create test data
    $author = Author::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $post1 = Post::create([
        'author_id' => $author->id,
        'title' => 'First Post',
        'content' => 'Content of first post',
        'published' => true,
    ]);

    $post2 = Post::create([
        'author_id' => $author->id,
        'title' => 'Second Post',
        'content' => 'Content of second post',
        'published' => false,
    ]);

    // Test the posts relationship
    $posts = $author->posts;
    expect($posts)->toHaveCount(2);
    expect($posts->first()->title)->toBe('First Post');

    // Test the publishedPosts relationship with constraints
    $publishedPosts = $author->publishedPosts;
    expect($publishedPosts)->toHaveCount(1);
    expect($publishedPosts->first()->title)->toBe('First Post');
});

it('can create and use BelongsTo relationships defined with attributes', function () {
    // Create test data
    $author = Author::create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $post = Post::create([
        'author_id' => $author->id,
        'title' => 'Test Post',
        'content' => 'Test content',
        'published' => true,
    ]);

    // Test the author relationship
    $postAuthor = $post->author;
    expect($postAuthor)->toBeInstanceOf(Author::class);
    expect($postAuthor->name)->toBe('Jane Doe');
    expect($postAuthor->id)->toBe($author->id);
});

it('can create and use BelongsToMany relationships defined with attributes', function () {
    // Create test data
    $author = Author::create([
        'name' => 'Bob Smith',
        'email' => 'bob@example.com',
    ]);

    $post = Post::create([
        'author_id' => $author->id,
        'title' => 'Tagged Post',
        'content' => 'Post with tags',
        'published' => true,
    ]);

    $tag1 = Tag::create(['name' => 'Laravel']);
    $tag2 = Tag::create(['name' => 'PHP']);

    // Attach tags to post
    $post->tags()->attach([$tag1->id, $tag2->id]);

    // Test the tags relationship from post
    $postTags = $post->tags;
    expect($postTags)->toHaveCount(2);
    expect($postTags->pluck('name')->toArray())->toContain('Laravel', 'PHP');

    // Test the posts relationship from tag
    $tagPosts = $tag1->posts;
    expect($tagPosts)->toHaveCount(1);
    expect($tagPosts->first()->title)->toBe('Tagged Post');
});

it('supports nested relationship queries', function () {
    // Create test data
    $author = Author::create([
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com',
    ]);

    $post = Post::create([
        'author_id' => $author->id,
        'title' => 'Post with Comments',
        'content' => 'This post has comments',
        'published' => true,
    ]);

    Comment::create([
        'post_id' => $post->id,
        'content' => 'First comment',
    ]);

    Comment::create([
        'post_id' => $post->id,
        'content' => 'Second comment',
    ]);

    // Test nested relationships: author -> posts -> comments
    $authorWithPosts = Author::with('posts.comments')->find($author->id);
    expect($authorWithPosts->posts)->toHaveCount(1);
    expect($authorWithPosts->posts->first()->comments)->toHaveCount(2);
});

it('can query relationships using Eloquent methods', function () {
    // Create test data
    $author = Author::create([
        'name' => 'Charlie Brown',
        'email' => 'charlie@example.com',
    ]);

    Post::create([
        'author_id' => $author->id,
        'title' => 'Published Post',
        'content' => 'This is published',
        'published' => true,
    ]);

    Post::create([
        'author_id' => $author->id,
        'title' => 'Draft Post',
        'content' => 'This is a draft',
        'published' => false,
    ]);

    // Test querying the relationship
    $publishedCount = $author->posts()->where('published', true)->count();
    expect($publishedCount)->toBe(1);

    $draftCount = $author->posts()->where('published', false)->count();
    expect($draftCount)->toBe(1);
});
