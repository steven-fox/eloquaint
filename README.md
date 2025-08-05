# Eloquaint - Reduce the boilerplate in your Eloquent classes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/steven-fox/eloquaint.svg?style=flat-square)](https://packagist.org/packages/steven-fox/eloquaint)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/steven-fox/eloquaint/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/steven-fox/eloquaint/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/steven-fox/eloquaint/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/steven-fox/eloquaint/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/steven-fox/eloquaint.svg?style=flat-square)](https://packagist.org/packages/steven-fox/eloquaint)

Eloquaint allows you to define Laravel Eloquent model relationships using PHP attributes instead of traditional methods, dramatically reducing boilerplate code and improving readability.

## Installation

You can install the package via composer:

```bash
composer require steven-fox/eloquaint
```

## Basic Usage

Instead of writing traditional relationship methods:

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
}
```

You can now use attributes:

```php
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

#[HasMany(Post::class)]
#[HasMany(Post::class, name: 'publishedPosts', where: ['published' => true])]
class Author extends Model
{
    use HasAttributeRelations;

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
    use HasAttributeRelations;
}
```

### One-to-One Relationships

```php
use StevenFox\Eloquaint\Attributes\HasOne;

#[HasOne(Profile::class)]
class User extends Model
{
    use HasAttributeRelations;
}
```

### Inverse Relationships

```php
use StevenFox\Eloquaint\Attributes\BelongsTo;

#[BelongsTo(Author::class)]
#[BelongsTo(Category::class)]
class Post extends Model
{
    use HasAttributeRelations;
}
```

### Many-to-Many Relationships

```php
use StevenFox\Eloquaint\Attributes\BelongsToMany;

#[BelongsToMany(Tag::class)]
#[BelongsToMany(Category::class, table: 'post_categories')]
class Post extends Model
{
    use HasAttributeRelations;
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
    use HasAttributeRelations;
}
```

## Advanced Features

### Custom Relationship Names

```php
#[HasMany(Post::class, name: 'articles')]
#[HasMany(Post::class, name: 'publishedArticles', where: ['status' => 'published'])]
class Author extends Model
{
    use HasAttributeRelations;
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
    use HasAttributeRelations;
}
```

### Custom Foreign Keys

```php
#[BelongsTo(User::class, foreignKey: 'user_id', ownerKey: 'id')]
#[HasMany(Comment::class, foreignKey: 'post_id', localKey: 'id')]
class Post extends Model
{
    use HasAttributeRelations;
}
```

### Property-Level Attributes

You can also define relationships on properties:

```php
class Author extends Model
{
    use HasAttributeRelations;

    #[HasMany(Post::class)]
    protected $posts;

    #[HasMany(Post::class, where: ['published' => true])]
    protected $publishedPosts;
}
```

## How It Works

1. **Add the trait**: Include `HasAttributeRelations` in your model
2. **Define relationships**: Use PHP attributes on your class or properties
3. **Use normally**: Access relationships exactly like traditional Eloquent relationships

The package automatically:
- Resolves relationship names (e.g., `Post::class` becomes `posts`)
- Handles foreign key conventions
- Applies query constraints
- Caches relationship definitions for performance

## Performance

Eloquaint is designed for performance:
- Relationship definitions are cached after first resolution
- No runtime overhead compared to traditional relationships
- Lazy loading and eager loading work exactly the same
- All Eloquent relationship features are preserved

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
