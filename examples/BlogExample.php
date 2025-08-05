<?php

declare(strict_types=1);

/**
 * Example demonstrating Eloquaint package usage in a blog application.
 *
 * This example shows how to define various relationship types using PHP attributes
 * instead of traditional Eloquent relationship methods.
 */

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\BelongsToMany;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\HasOne;
use StevenFox\Eloquaint\Attributes\MorphMany;
use StevenFox\Eloquaint\Attributes\MorphTo;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

/**
 * User model with profile and posts relationships.
 */
#[HasOne(Profile::class)]
#[HasMany(Post::class, name: 'posts')]
#[HasMany(Post::class, name: 'publishedPosts', where: ['published' => true])]
#[HasMany(Comment::class, name: 'comments')]
class User extends Model
{
    use HasAttributeRelations;

    protected $fillable = ['name', 'email'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
}

/**
 * Profile model belonging to a user.
 */
#[BelongsTo(User::class)]
class Profile extends Model
{
    use HasAttributeRelations;

    protected $fillable = ['user_id', 'bio', 'avatar_url', 'website'];
}

/**
 * Post model with author, comments, tags, and images.
 */
#[BelongsTo(User::class, name: 'author')]
#[HasMany(Comment::class)]
#[BelongsToMany(Tag::class)]
#[MorphMany(Image::class, name: 'imageable')]
class Post extends Model
{
    use HasAttributeRelations;

    protected $fillable = ['user_id', 'title', 'content', 'published', 'featured'];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}

/**
 * Comment model with author and commentable polymorphic relationship.
 */
#[BelongsTo(User::class, name: 'author')]
#[MorphTo(name: 'commentable')]
class Comment extends Model
{
    use HasAttributeRelations;

    protected $fillable = ['user_id', 'content', 'commentable_type', 'commentable_id'];
}

/**
 * Tag model with posts relationship.
 */
#[BelongsToMany(Post::class)]
class Tag extends Model
{
    use HasAttributeRelations;

    protected $fillable = ['name', 'slug'];
}

/**
 * Image model with polymorphic imageable relationship.
 */
#[MorphTo(name: 'imageable')]
class Image extends Model
{
    use HasAttributeRelations;

    protected $fillable = ['url', 'alt_text', 'imageable_type', 'imageable_id'];
}

/**
 * Example usage demonstrating the relationships work exactly like traditional Eloquent.
 */
function demonstrateUsage(): void
{
    // Create a user with profile
    $user = User::create(['name' => 'John Doe', 'email' => 'john@example.com']);
    $profile = Profile::create(['user_id' => $user->id, 'bio' => 'Laravel developer']);

    // Create posts
    $post1 = Post::create([
        'user_id' => $user->id,
        'title' => 'Getting Started with Eloquaint',
        'content' => 'This package makes relationships so much cleaner...',
        'published' => true,
    ]);

    $post2 = Post::create([
        'user_id' => $user->id,
        'title' => 'Draft Post',
        'content' => 'This is still a draft...',
        'published' => false,
    ]);

    // Create tags and attach to posts
    $tag1 = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
    $tag2 = Tag::create(['name' => 'PHP', 'slug' => 'php']);
    $post1->tags()->attach([$tag1->id, $tag2->id]);

    // Create comments
    Comment::create([
        'user_id' => $user->id,
        'content' => 'Great post!',
        'commentable_type' => Post::class,
        'commentable_id' => $post1->id,
    ]);

    // Usage examples - these work exactly like traditional Eloquent relationships:

    // Access user's profile
    echo $user->profile->bio; // "Laravel developer"

    // Access all posts
    echo $user->posts->count(); // 2

    // Access only published posts (with where constraint)
    echo $user->publishedPosts->count(); // 1

    // Access post author
    echo $post1->author->name; // "John Doe"

    // Access post tags
    foreach ($post1->tags as $tag) {
        echo $tag->name; // "Laravel", "PHP"
    }

    // Access comments on a post
    foreach ($post1->comments as $comment) {
        echo $comment->content; // "Great post!"
        echo $comment->author->name; // "John Doe"
    }

    // Eager loading works the same
    $usersWithPosts = User::with('posts.tags')->get();

    // Query relationships
    $publishedPosts = $user->posts()->where('published', true)->get();
    $featuredPosts = $user->posts()->where('featured', true)->get();

    // Everything works exactly like traditional Eloquent relationships!
}
