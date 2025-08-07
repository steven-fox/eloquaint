# Experimental!
This project is a work in progress. Expect breaking changes. 

# Eloquaint - Reduce the boilerplate in your Eloquent classes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/steven-fox/eloquaint.svg?style=flat-square)](https://packagist.org/packages/steven-fox/eloquaint)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/steven-fox/eloquaint/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/steven-fox/eloquaint/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/steven-fox/eloquaint/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/steven-fox/eloquaint/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/steven-fox/eloquaint.svg?style=flat-square)](https://packagist.org/packages/steven-fox/eloquaint)

Eloquaint allows you to define Laravel Eloquent model relationships and scopes using PHP attributes instead of traditional methods, reducing boilerplate code.

## PHP 8.5 Closures as Constant Values
In PHP 8.5, it will be possible to define a closure as a "constant value". In theory, this will enable syntax like:

```php
#[Scope('published', static function ($query) {$query->whereNotNull('published_at')->where('published_at', '<=', now())})]

#[HasMany(Notification::class, 'readNotifications', static function ($query) {$query->whereNotNull('read_at')})]
```

Thus, we will incorporate these features once PHP 8.5 hits GA and tag a v1.0 release of this package.

## Installation

You can install the package via composer:

```bash
composer require steven-fox/eloquaint
```

## Basic Usage

Instead of writing traditional relationship and scope methods:

```php
class Author extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(Post::class)->where('published', true);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
```

You can now use attributes:

```php
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\Scope;
use StevenFox\Eloquaint\Traits\HasEloquaintFeatures;

#[HasMany(Post::class)]
#[HasMany(Post::class, name: 'publishedPosts', where: ['published' => true])]
#[Scope('active', 'active', true)]
class Author extends Model
{
    use HasEloquaintFeatures;

    // That's it! No boilerplate methods needed.
}
```

## Supported Relationships

Eloquaint supports all Laravel relationship types:

### One-to-Many Relationships

```php
use StevenFox\Eloquaint\Attributes\HasMany;

#[HasMany(Post::class)]
#[HasMany(Comment::class)]
class Author extends Model
{
    use HasEloquaintFeatures;
}
```

### One-to-One Relationships

```php
use StevenFox\Eloquaint\Attributes\HasOne;

#[HasOne(Profile::class)]
class User extends Model
{
    use HasEloquaintFeatures;
}
```

### Inverse Relationships

```php
use StevenFox\Eloquaint\Attributes\BelongsTo;

#[BelongsTo(Author::class)]
#[BelongsTo(Category::class)]
class Post extends Model
{
    use HasEloquaintFeatures;
}
```

### Many-to-Many Relationships

```php
use StevenFox\Eloquaint\Attributes\BelongsToMany;

#[BelongsToMany(Tag::class)]
#[BelongsToMany(Category::class, table: 'post_categories')]
class Post extends Model
{
    use HasEloquaintFeatures;
}
```

### Advanced Relationships

```php
use StevenFox\Eloquaint\Attributes\HasManyThrough;
use StevenFox\Eloquaint\Attributes\MorphMany;

#[HasManyThrough(Comment::class, through: Post::class)]
#[MorphMany(Image::class, name: 'imageable')]
class Author extends Model
{
    use HasEloquaintFeatures;
}
```

## Advanced Features

### Custom Relationship Names

```php
#[HasMany(Post::class, name: 'articles')]
#[HasMany(Post::class, name: 'publishedArticles', where: ['status' => 'published'])]
class Author extends Model
{
    use HasEloquaintFeatures;
}

// Usage:
$author->articles; // All posts
$author->publishedArticles; // Only published posts
```

### Query Constraints

Add where clauses directly to your relationship definitions:

```php
#[HasMany(Post::class, where: ['published' => true, 'featured' => true])]
class Author extends Model
{
    use HasEloquaintFeatures;
}
```

### Custom Foreign Keys

```php
#[BelongsTo(User::class, foreignKey: 'user_id', ownerKey: 'id')]
#[HasMany(Comment::class, foreignKey: 'post_id', localKey: 'id')]
class Post extends Model
{
    use HasEloquaintFeatures;
}
```

### Property-Level Attributes

You can also define relationships on properties:

```php
class Author extends Model
{
    use HasEloquaintFeatures;

    #[HasMany(Post::class)]
    protected $posts;

    #[HasMany(Post::class, where: ['published' => true])]
    protected $publishedPosts;
}
```

## Supported Scopes

Eloquaint also supports defining local scopes using attributes:

### Simple Scopes

For basic where clauses, you can define scopes directly:

```php
use StevenFox\Eloquaint\Attributes\Scope;

#[Scope('published', 'published', true)]           // WHERE published = true
#[Scope('draft', 'published', false)]              // WHERE published = false
#[Scope('popular', 'views', '>', 1000)]            // WHERE views > 1000
class Post extends Model
{
    use HasEloquaintFeatures;
}

// Usage
$publishedPosts = Post::published()->get();
$popularPosts = Post::popular()->get();
```

### Complex Scopes

For complex logic, use traditional scope methods alongside simple attribute scopes:

```php
#[Scope('published', 'published', true)]
#[Scope('popular', 'views', '>', 1000)]
class Post extends Model
{
    use HasEloquaintFeatures;

    // Use traditional scope methods for complex logic
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeTrending($query)
    {
        return $query->where('views', '>=', 1000)->where('likes', '>=', 10);
    }
}

// Usage
$publishedPosts = Post::published()->get();     // Attribute scope
$popularPosts = Post::popular()->get();         // Attribute scope
$recentPosts = Post::recent(14)->get();         // Traditional scope
$trendingPosts = Post::trending()->get();       // Traditional scope
```

### Chaining with Query Methods

Scopes can be chained with regular query methods:

```php
$posts = Post::published()
    ->where('title', 'like', '%Laravel%')
    ->with('author')
    ->orderBy('created_at', 'desc')
    ->get();

// For multiple scopes, apply them to the base query
$recentPublishedPosts = Post::published()->where('created_at', '>=', now()->subDays(7))->get();
$popularPosts = Post::popular()->get();
```

## How It Works

1. **Add the trait**: Include `HasEloquaintFeatures` in your model (or `HasAttributeRelations` for relationships only)
2. **Define relationships and scopes**: Use PHP attributes on your class
3. **Use normally**: Access relationships and scopes exactly like traditional Eloquent

The package automatically:
- Resolves relationship names (e.g., `Post::class` becomes `posts`)
- Handles foreign key conventions
- Applies query constraints and scope logic
- Caches definitions for performance
- Supports both static and instance method calls for scopes

## Performance

Eloquaint is designed for performance:
- Relationship and scope definitions are cached after first resolution
- No runtime overhead compared to traditional relationships and scopes
- Lazy loading and eager loading work exactly the same
- All Eloquent relationship and scope features are preserved
- Scopes work with both static and instance calls

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Steven Fox](https://github.com/steven-fox)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
