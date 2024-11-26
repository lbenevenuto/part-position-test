<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    /** @use HasFactory<\Database\Factories\BlockFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'title',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function blockFields(): HasMany
    {
        return $this->hasMany(BlockField::class);
    }

    public function medias(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function medium(): HasMany
    {
        return $this->hasMany(Media::class);
    }
}
