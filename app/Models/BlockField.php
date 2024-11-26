<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockField extends Model
{
    /** @use HasFactory<\Database\Factories\BlockFieldFactory> */
    use HasFactory;

    protected $fillable=[
        'block_id',
        'type',
        'value',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}
