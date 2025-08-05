<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\HasMany;
use StevenFox\Eloquaint\Attributes\HasManyThrough;
use StevenFox\Eloquaint\Attributes\HasOneThrough;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

#[HasMany(User::class)]
#[HasManyThrough(Post::class, through: User::class)]
#[HasOneThrough(Profile::class, through: User::class)]
#[HasManyThrough(Post::class, through: User::class, name: 'customPosts', firstKey: 'country_id', secondKey: 'user_id')]
final class Country extends Model
{
    use HasAttributeRelations;

    protected $fillable = [
        'name',
        'code',
    ];

    public function casts(): array
    {
        return [];
    }
}
