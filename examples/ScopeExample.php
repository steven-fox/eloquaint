<?php

declare(strict_types=1);

/**
 * Example demonstrating Eloquaint scope functionality.
 *
 * This example shows how to define local scopes using PHP attributes
 * instead of traditional Eloquent scope methods.
 */

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\Scope;
use StevenFox\Eloquaint\Traits\HasEloquaintFeatures;

/**
 * Author model with both relationships and scopes defined via attributes.
 */
#[HasMany(Post::class)]
#[Scope('active', 'active', true)]
#[Scope('verified', 'email_verified_at', '!=', null)]
class Author extends Model
{
    use HasEloquaintFeatures;

    protected $fillable = ['name', 'email', 'active', 'email_verified_at'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }
}

/**
 * Post model demonstrating various scope types.
 */
#[BelongsTo(Author::class)]
#[BelongsTo(Category::class)]
#[HasMany(Comment::class)]
#[Scope('published', 'published', true)]
#[Scope('draft', 'published', false)]
#[Scope('featured', 'featured', true)]
#[Scope('popular', 'views', '>', 1000)]
class Post extends Model
{
    use HasEloquaintFeatures;

    protected $fillable = [
        'title',
        'content',
        'published',
        'featured',
        'author_id',
        'category_id',
        'views',
        'likes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * Category model with simple scopes.
 */
#[HasMany(Post::class)]
#[Scope('active', 'active', true)]
#[Scope('featured', 'featured', true)]
class Category extends Model
{
    use HasEloquaintFeatures;

    protected $fillable = ['name', 'slug', 'active', 'featured'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'featured' => 'boolean',
        ];
    }
}

/**
 * Comment model demonstrating scope chaining.
 */
#[BelongsTo(Post::class)]
#[BelongsTo(Author::class, name: 'commenter')]
#[Scope('approved', 'approved', true)]
#[Scope('pending', 'approved', false)]
class Comment extends Model
{
    use HasEloquaintFeatures;

    protected $fillable = ['content', 'approved', 'post_id', 'author_id'];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

// Usage examples:

// Simple scopes (using simple where clauses)
$publishedPosts = Post::published()->get();
$draftPosts = Post::draft()->get();
$featuredPosts = Post::featured()->get();
$popularPosts = Post::popular()->get();
$activeAuthors = Author::active()->get();
$approvedComments = Comment::approved()->get();

// Combining scopes with regular query methods
$posts = Post::published()
    ->where('title', 'like', '%Laravel%')
    ->with('author', 'category')
    ->orderBy('created_at', 'desc')
    ->paginate(10);

// Using scopes with relationships
$activeAuthorsWithPublishedPosts = Author::active()
    ->whereHas('posts', function ($query) {
        $query->published();
    })
    ->get();

// Complex queries combining scopes with regular query methods
$complexQuery = Post::published()
    ->where('title', 'like', '%Laravel%')
    ->with(['author' => function ($query) {
        $query->where('active', true);
    }])
    ->whereHas('category', function ($query) {
        $query->where('active', true);
    })
    ->orderBy('created_at', 'desc')
    ->get();
