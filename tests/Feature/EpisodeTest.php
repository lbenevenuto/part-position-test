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

class EpisodeTest extends TestCase
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

    public function test_can_list_episodes()
    {
        $response = $this->get('/api/episodes');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_can_view_single_episode_with_parts()
    {
        $response = $this->get("/api/episodes/{$this->episode->id}?withParts=1");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at',
                    'parts' => [
                        '*' => [
                            'id',
                            'episode_id',
                            'position',
                            'title',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ]);
    }

    public function test_can_create_an_episode()
    {
        $data     = [
            'title' => 'New Episode',
        ];

        $response = $this->post('/api/episodes', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('episodes', $data);
    }

    public function test_can_update_an_episode()
    {
        $data     = [
            'title' => 'Updated Episode Title',
        ];

        $response = $this->put("/api/episodes/{$this->episode->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('episodes', $data);
    }

    public function test_can_delete_an_episode()
    {
        $response = $this->delete("/api/episodes/{$this->episode->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('episodes', ['id' => $this->episode->id]);
        $this->assertDatabaseMissing('parts', ['episode_id' => $this->episode->id]);
    }

    public function test_can_duplicate_an_episode()
    {
        $copyString       = ' (Copy)';
        $response         = $this->post("/api/episodes/{$this->episode->id}/duplicate");

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'title' => $this->episode->title . $copyString,
        ]);
        $this->assertDatabaseHas('episodes', ['title' => $this->episode->title . $copyString]);
        $this->assertNotEquals($this->episode->id, $response->json('data.id'));

        // Check all relations are duplicated
        $missingRelations = ['parts', 'parts.items', 'parts.items.blocks', 'parts.items.blocks.blockFields', 'parts.items.blocks.medias'];
        $this->episode->loadMissing($missingRelations);
        $newEpisode       = Episode::with($missingRelations)->find($response->json('data.id'));
        $this->assertEquals($this->episode->parts->count(), $newEpisode->parts->count());
        $this->assertEquals($this->episode->parts->pluck('title')->map(fn ($title) => $title . $copyString)->all(), $newEpisode->parts->pluck('title')->all());
        $this->assertEquals($this->episode->parts->first()->items()->count(), $newEpisode->parts->first()->items()->count());
        $this->assertEquals($this->episode->parts->first()->items->first()->blocks()->count(), $newEpisode->parts->first()->items->first()->blocks()->count());
        $this->assertEquals($this->episode->parts->first()->items->first()->blocks->first()->blockFields()->count(), $newEpisode->parts->first()->items->first()->blocks->first()->blockFields()->count());
        $this->assertEquals($this->episode->parts->first()->items->first()->blocks->first()->medias()->count(), $newEpisode->parts->first()->items->first()->blocks->first()->medias()->count());
    }
}
