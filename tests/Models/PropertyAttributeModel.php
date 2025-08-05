<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\MorphMany;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

final class PropertyAttributeModel extends Model
{
    use HasAttributeRelations;

    #[HasMany(Post::class)]
    public $posts;

    #[BelongsTo(Author::class)]
    public $author;

    #[MorphMany(Image::class, name: 'imageable')]
    public $images;

    protected $fillable = [
        'name',
        'author_id',
    ];

    public function casts(): array
    {
        return [];
    }
}
