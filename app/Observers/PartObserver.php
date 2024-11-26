<?php

namespace App\Observers;

use App\Models\Part;

class PartObserver
{
    public function creating(Part $part): void
    {
        logger(__METHOD__ . ' triggered');

        Part::where('episode_id', $part->episode_id)
            ->where('position', '>=', $part->position)
            ->increment('position');
    }

    public function updating(Part $part): void
    {
        logger(__METHOD__ . ' triggered');

        if ($part->isDirty('position')) {
            logger('position is dirty');

            $originalPosition = $part->getOriginal('position');

            if ($originalPosition < $part->position) {
                Part::where('episode_id', $part->episode_id)
                    ->whereBetween('position', [$originalPosition + 1, $part->position])
                    ->decrement('position');
            } elseif ($originalPosition > $part->position) {
                Part::where('episode_id', $part->episode_id)
                    ->whereBetween('position', [$part->position, $originalPosition - 1])
                    ->increment('position');
            }
        }
    }

    public function deleting(Part $part): void
    {
        logger(__METHOD__ . ' triggered');

        Part::where('episode_id', $part->episode_id)
            ->where('position', '>', $part->position)
            ->decrement('position');
    }
}
