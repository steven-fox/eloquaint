<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\BelongsToMany;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\MorphMany;
use StevenFox\Eloquaint\Attributes\MorphOne;
use StevenFox\Eloquaint\Attributes\MorphToMany;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

/**
 * Test model for Post with attribute relationships.
 *
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $content
 * @property bool $published
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Author $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tag> $tags
 */
#[BelongsTo(Author::class)]
#[HasMany(Comment::class)]
#[BelongsToMany(Tag::class)]
#[MorphMany(Image::class, name: 'imageable')]
#[MorphOne(Video::class, name: 'videoable')]
#[MorphToMany(Tag::class, name: 'taggable', relationName: 'morphTags')]
final class Post extends Model
{
    use HasAttributeRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'user_id',
        'title',
        'content',
        'published',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
