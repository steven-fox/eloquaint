<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\BelongsTo;
use StevenFox\Eloquaint\Attributes\MorphMany;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

#[BelongsTo(User::class)]
#[MorphMany(Image::class, name: 'imageable')]
final class Profile extends Model
{
    use HasAttributeRelations;

    protected $fillable = [
        'bio',
        'avatar',
        'user_id',
    ];

    public function casts(): array
    {
        return [];
    }
}
