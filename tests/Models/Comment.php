<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\MorphTo;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

/**
 * Test model for Comment with attribute relationships.
 *
 * @property int $id
 * @property int $post_id
 * @property string $content
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Post $post
 */
#[BelongsTo(Post::class)]
#[BelongsTo(User::class, name: 'authorWithoutRelation')]
#[MorphTo(name: 'commentable')]
#[MorphTo(relationName: 'morphableWithoutName')]
final class Comment extends Model
{
    use HasAttributeRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'post_id',
        'content',
        'user_id',
        'author_without_relation_id',
        'commentable_type',
        'commentable_id',
        'morphable_type',
        'morphable_id',
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
