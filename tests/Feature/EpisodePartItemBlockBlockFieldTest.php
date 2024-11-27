<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\BlockField;
use App\Models\Episode;
use App\Models\Item;
use App\Models\Media;
use App\Models\Part;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodePartItemBlockBlockFieldTest extends TestCase
{
    use RefreshDatabase;

    private Episode $episode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->episode = Episode::factory()
            ->has(
                Part::factory()
                    ->count(1)
                    ->has(
                        Item::factory()
                            ->has(
                                Block::factory()
                                    ->has(BlockField::factory()->count(1))
                                    ->has(Media::factory()->count(1))
                                    ->count(1)
                            )
                            ->count(1)
                    )
                    ->sequence(
                        ['position' => 0],
                    )
            )
            ->create();
    }

    public function test_can_list_block_fields()
    {
        $part       = $this->episode->parts->first();
        $item       = $part->items->first();
        $block      = $item->blocks->first();
        $blockField = $block->blockFields->first();
        $response   = $this->get("/api/episodes/{$this->episode->id}/parts/{$part->id}/items/{$item->id}/blocks/{$block->id}/block-fields");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'block_id',
                    'type',
                    'value',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $response->assertJsonFragment($blockField->toArray());
    }

    public function test_can_create_a_block_field()
    {
        $part     = $this->episode->parts->first();
        $item     = $part->items->first();
        $block    = $item->blocks->first();
        $data     = [
            'type' => 'New Block Field Type',
            'value' => 'New Block Field Value',
        ];

        $response = $this->post("/api/episodes/{$this->episode->id}/parts/{$part->id}/items/{$item->id}/blocks/{$block->id}/block-fields", $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'block_id',
                'type',
                'value',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_can_view_single_block_field()
    {
        $part       = $this->episode->parts->first();
        $item       = $part->items->first();
        $block      = $item->blocks->first();
        $blockField = $block->blockFields->first();
        $response   = $this->get("/api/episodes/{$this->episode->id}/parts/{$part->id}/items/{$item->id}/blocks/{$block->id}/block-fields/{$blockField->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'block_id',
                'type',
                'value',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonFragment($blockField->toArray());
    }

    public function test_can_update_a_block_field()
    {
        $part       = $this->episode->parts->first();
        $item       = $part->items->first();
        $block      = $item->blocks->first();
        $blockField = $block->blockFields->first();
        $data       = [
            'type' => 'New Block Field Type UPDATED',
        ];

        $response   = $this->put("/api/episodes/{$this->episode->id}/parts/{$part->id}/items/{$item->id}/blocks/{$block->id}/block-fields/{$blockField->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'block_id',
                'type',
                'value',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonFragment($data);
    }

    public function test_can_delete_a_block_field()
    {
        $part       = $this->episode->parts->first();
        $item       = $part->items->first();
        $block      = $item->blocks->first();
        $blockField = $block->blockFields->first();
        $response   = $this->delete("/api/episodes/{$this->episode->id}/parts/{$part->id}/items/{$item->id}/blocks/{$block->id}/block-fields/{$blockField->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('block_fields', ['id' => $blockField->id]);
    }
}
