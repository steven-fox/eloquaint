<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use StevenFox\Eloquaint\Attributes\MorphTo;
use StevenFox\Eloquaint\Traits\HasAttributeRelations;

#[MorphTo(name: 'imageable')]
#[MorphTo(name: 'customImageable', relationName: 'customRelation')]
final class Image extends Model
{
    use HasAttributeRelations;

    protected $fillable = [
        'url',
        'imageable_type',
        'imageable_id',
        'active',
    ];

    public function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }
}
