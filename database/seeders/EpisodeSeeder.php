<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\BlockField;
use App\Models\Episode;
use App\Models\Item;
use App\Models\Media;
use App\Models\Part;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EpisodeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $episodeCount    = 5;
        $partCount       = 4;
        $itemCount       = 3;
        $blockCount      = 2;
        $blockFieldCount = 1;
        $mediaCount      = 1;

        Episode::factory()
            ->count($episodeCount)
            ->has(
                Part::factory()
                    ->count($partCount)
                    ->has(
                        Item::factory()
                            ->has(
                                Block::factory()
                                    ->has(BlockField::factory()->count($blockFieldCount))
                                    ->has(Media::factory()->count($mediaCount))
                                    ->count($blockCount)
                            )
                            ->count($itemCount)
                    )
                    ->sequence(
                        ['position' => 0],
                        ['position' => 1],
                        ['position' => 2],
                        ['position' => 3],
                        ['position' => 4],
                    )
            )
            ->create();
    }
}
