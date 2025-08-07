<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\Scope;
use StevenFox\Eloquaint\Traits\HasEloquaintFeatures;

/**
 * Test model with both relationships and scopes defined via attributes.
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property bool $published
 * @property int $author_id
 * @property int $category_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
#[BelongsTo(Author::class)]
#[BelongsTo(Category::class)]
#[HasMany(Comment::class)]
#[Scope('published', 'published', true)]
#[Scope('draft', 'published', false)]
#[Scope('popular', 'views', '>', 1000)]
final class PostWithScopes extends Model
{
    use HasEloquaintFeatures;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'content',
        'published',
        'author_id',
        'category_id',
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
