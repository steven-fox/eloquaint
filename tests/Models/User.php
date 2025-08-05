<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\HasOne;
use StevenFox\Eloquaint\Attributes\MorphMany;
use StevenFox\Eloquaint\Attributes\MorphOne;
use StevenFox\Eloquaint\Attributes\MorphToMany;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

#[BelongsTo(Country::class)]
#[HasMany(Post::class)]
#[HasOne(Profile::class)]
#[MorphMany(Image::class, name: 'imageable')]
#[MorphOne(Video::class, name: 'videoable')]
#[MorphToMany(Tag::class, name: 'taggable')]
#[MorphMany(Image::class, name: 'imageable', relationName: 'customImages', where: ['active' => true])]
final class User extends Model
{
    use HasAttributeRelations;

    protected $fillable = [
        'name',
        'email',
        'country_id',
    ];

    public function casts(): array
    {
        return [];
    }
}
