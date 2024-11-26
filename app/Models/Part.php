<?php

namespace App\Models;

use App\Observers\PartObserver;
use DB;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([PartObserver::class])]
class Part extends Model
{
    /** @use HasFactory<\Database\Factories\PartFactory> */
    use HasFactory;

    protected $fillable = [
        'episode_id',
        'position',
        'title',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Customize the default query to include a default position order.
     */
    public function newQuery(): Builder
    {
        return parent::newQuery()->orderBy('position');
    }

    /**
     * Bulk update the positions of the parts.
     */
    public static function bulkUpdatePositions(array $positions, int $episodeId): void
    {
        logger(__METHOD__ . ' triggered');

        $cases   = [];
        $ids     = [];
        foreach ($positions as $item) {
            $cases[] = "WHEN id = {$item['id']} THEN {$item['position']}";
            $ids[]   = $item['id'];
        }

        $caseSql = implode(' ', $cases);
        $idList  = implode(', ', $ids);

        $sql     = sprintf('
            UPDATE parts
            SET position = CASE
                %s
            END
            WHERE id IN (%s) AND episode_id = %d
            ', $caseSql, $idList, $episodeId);

        DB::statement($sql);
    }
}
