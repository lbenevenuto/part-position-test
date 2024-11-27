<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class Episode extends Model
{
    /** @use HasFactory<\Database\Factories\EpisodeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function sortParts(array $positions): void
    {
        logger(__METHOD__ . ' triggered');

        $updateArray = [];
        foreach ($positions as $newPosition => $partId) {
            $updateArray[] = [
                'id' => $partId,
                'position' => $newPosition,
            ];
        }

        Part::bulkUpdatePositions($updateArray, $this->id);
    }

    /**
     * @throws Throwable
     */
    public function duplicate(): Episode
    {
        logger(__METHOD__ . ' triggered');
        $copyString        = ' (Copy)';
        $newEpisode        = $this->replicate();
        $newEpisode->title = $newEpisode->title . $copyString;

        DB::transaction(function () use ($newEpisode, $copyString) {
            $newEpisode->save();

            foreach ($this->parts as $part) {
                $newPart             = $part->replicate();
                $newPart->episode_id = $newEpisode->id;
                $newPart->title      = $newPart->title . $copyString;
                $newPart->save();

                foreach ($part->items as $item) {
                    $newItem          = $item->replicate();
                    $newItem->part_id = $newPart->id;
                    $newItem->title   = $newItem->title . $copyString;
                    $newItem->save();

                    foreach ($item->blocks as $block) {
                        $newBlock          = $block->replicate();
                        $newBlock->item_id = $newItem->id;
                        $newBlock->title   = $newBlock->title . $copyString;
                        $newBlock->save();

                        foreach ($block->blockFields as $blockField) {
                            $newBlockField           = $blockField->replicate();
                            $newBlockField->block_id = $newBlock->id;
                            $newBlockField->value    = $newBlockField->value . $copyString;
                            $newBlockField->save();
                        }

                        foreach ($block->medias as $media) {
                            $newMedia           = $media->replicate();
                            $newMedia->block_id = $newBlock->id;
                            $newMedia->save();
                        }
                    }
                }
            }
        });

        return $newEpisode;
    }
}
