<?php

namespace Tests\Feature;

use App\Models\Episode;
use App\Models\Part;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartTest extends TestCase
{
    use RefreshDatabase;

    protected Episode $episode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->episode = Episode::factory()
            ->has(Part::factory()
                ->sequence(
                    ['position' => 0],
                    ['position' => 1],
                    ['position' => 2],
                    ['position' => 3],
                    ['position' => 4],
                    ['position' => 5],
                    ['position' => 6],
                    ['position' => 7],
                    ['position' => 8],
                    ['position' => 9],
                )
                ->count(10))
            ->create();
    }

    public function test_can_list_parts()
    {
        $response = $this->get('/api/parts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'episode_id',
                    'position',
                    'title',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_can_view_single_part()
    {
        $part     = $this->episode->parts->first();
        $response = $this->get("/api/parts/{$part->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'episode_id',
                'position',
                'title',
                'created_at',
                'updated_at',
            ],
        ]);
        $response->assertJson([
            'data' => $part->toArray(),
        ]);
    }

    public function test_can_create_a_part_at_the_beginning()
    {
        $data     = [
            'episode_id' => $this->episode->id,
            'position' => 0,
            'title' => 'New Part',
        ];

        $response = $this->post('/api/parts', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('parts', $data);
        $lastPart = $this->episode->parts->last();
        $this->assertEquals(10, $lastPart->position);
    }

    public function test_can_create_a_part_in_the_middle()
    {
        $data     = [
            'episode_id' => $this->episode->id,
            'position' => 5,
            'title' => 'New Part',
        ];

        $response = $this->post('/api/parts', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('parts', $data);
        $lastPart = $this->episode->parts->last();
        $this->assertEquals(10, $lastPart->position);
    }

    public function test_can_update_a_part_position_going_up()
    {
        $firstPart  = $this->episode->parts->first();
        $secondPart = $this->episode->parts->skip(1)->first();
        $data       = [
            'episode_id' => $this->episode->id,
            'position' => 5,
            'title' => $firstPart->title,
        ];

        $response   = $this->put("/api/parts/{$firstPart->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment($data);
        $this->episode->refresh();
        // Part 1 should now be in position 5 and Part 2 should be in position 0
        $this->assertEquals(5, $this->episode->parts->find($firstPart->id)->position);
        $this->assertEquals(0, $this->episode->parts->find($secondPart->id)->position);
    }

    public function test_can_update_a_part_position_going_down()
    {
        $parts      = $this->episode->parts;
        $lastPart   = $parts->last();
        $secondPart = $parts->reverse()->skip(1)->first();
        $data       = [
            'episode_id' => $this->episode->id,
            'position' => 5,
            'title' => $lastPart->title,
        ];

        $response   = $this->put("/api/parts/{$lastPart->id}", $data);
        $response->assertStatus(200);
        $response->assertJsonFragment($data);
        $this->episode->refresh();
        // Part 10 should now be in position 5 and Part 9 should be in position 10
        $this->assertEquals(5, $this->episode->parts->find($lastPart->id)->position);
        $this->assertEquals(9, $this->episode->parts->find($secondPart->id)->position);
    }

    public function test_can_delete_a_part()
    {
        $part     = $this->episode->parts->first();
        $response = $this->delete("/api/parts/{$part->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('parts', ['id' => $part->id]);
    }
}
