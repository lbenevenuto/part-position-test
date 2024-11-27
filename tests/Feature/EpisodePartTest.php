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

class EpisodePartTest extends TestCase
{
    use RefreshDatabase;

    protected Episode $episode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->episode = Episode::factory()
            ->has(
                Part::factory()
                    ->count(5)
                    ->has(
                        Item::factory()
                            ->has(
                                Block::factory()
                                    ->has(BlockField::factory()->count(2))
                                    ->has(Media::factory()->count(2))
                                    ->count(2)
                            )
                            ->count(2)
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

    public function test_can_list_episode_parts()
    {
        $response = $this->get("/api/episodes/{$this->episode->id}/parts");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'episode_id',
                    'position',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_can_create_an_episode_part()
    {
        $data     = [
            'episode_id' => $this->episode->id,
            'position' => 3,
            'title' => 'New Episode Position 3',
        ];

        $response = $this->post("/api/episodes/{$this->episode->id}/parts", $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('parts', $data);
    }

    public function test_can_view_single_episode_part()
    {
        $part     = $this->episode->parts->first();
        $response = $this->get("/api/episodes/{$this->episode->id}/parts/{$part->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'episode_id',
                'position',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJsonFragment($part->toArray());
    }

    public function test_can_update_an_episode_part()
    {
        $part     = $this->episode->parts->first();
        $data     = [
            'episode_id' => $part->episode_id,
            'position' => 4,
            'title' => $part->title,
        ];

        $response = $this->put("/api/episodes/{$this->episode->id}/parts/{$part->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment($data);
        $this->assertDatabaseHas('parts', $data);
    }

    public function test_can_delete_an_episode_part()
    {
        $part     = $this->episode->parts->first();
        $response = $this->delete("/api/episodes/{$this->episode->id}/parts/{$part->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('parts', ['id' => $part->id]);
    }

    public function test_can_sort_an_episode_parts()
    {
        $parts        = $this->episode->parts;
        $newPositions = $parts->pluck('id')->shuffle()->toArray();
        $data         = [
            'positions' => $newPositions,
        ];
        $response     = $this->post("/api/episodes/{$this->episode->id}/parts/sort", $data);

        $response->assertStatus(204);
        // Check if the parts are sorted correctly
        $this->episode->refresh();
        $parts        = $this->episode->parts;
        $lala         = $parts->sortBy('position')->pluck('id')->toArray();
        $this->assertEquals($newPositions, $lala);
    }
}
