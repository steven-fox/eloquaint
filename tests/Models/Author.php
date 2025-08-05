<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

/**
 * Test model for Author with attribute relationships.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $publishedPosts
 */
#[HasMany(Post::class)]
#[HasMany(Post::class, name: 'publishedPosts', where: ['published' => true])]
final class Author extends Model
{
    use HasAttributeRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
