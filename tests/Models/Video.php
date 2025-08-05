<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\MorphTo;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

#[MorphTo(name: 'videoable')]
final class Video extends Model
{
    use HasAttributeRelations;

    protected $fillable = [
        'url',
        'videoable_type',
        'videoable_id',
    ];

    public function casts(): array
    {
        return [];
    }
}
