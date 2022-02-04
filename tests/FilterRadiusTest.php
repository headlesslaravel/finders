<?php

namespace HeadlessLaravel\Finders\Tests;

use HeadlessLaravel\Finders\Tests\Fixtures\Models\Post;

class FilterRadiusTest extends TestCase
{
    protected $useMysql = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->building = Post::create([
            'title'     => 'Chrysler Building',
            'latitude'  => 40.75178128662803,
            'longitude' => -73.97552835820437,
        ]);

        $this->zoo = Post::create([
            'title'     => 'Central Park Zoo',
            'latitude'  => 40.76850772506696,
            'longitude' => -73.97186950177363,
        ]);
    }

    public function test_filtering_radius()
    {
        $this->get('posts?distance=2&latitude=40.75178128662803&longitude=-73.97552835820437')
            ->assertJsonCount(2, 'data');

        $this->get('posts?distance=1.1712&latitude=40.75178128662803&longitude=-73.97552835820437')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $this->building->id);

        $this->get('posts?distance=0.5&latitude=40.76850772506696&longitude=-73.97186950177363')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $this->zoo->id);
    }

    public function test_radius_required_keys()
    {
        $this->get('posts?distance=2&longitude=-73.97552835820437')
            ->assertInvalid([
                'latitude' => 'The latitude field is required when longitude / distance is present.',
            ]);

        $this->get('posts?distance=1.1712&latitude=40.75178128662803')
            ->assertInvalid([
                'longitude' => 'The longitude field is required when latitude / distance is present.',
            ]);

        $this->get('posts?latitude=40.76850772506696&longitude=-73.97186950177363')
            ->assertInvalid([
                'distance' => 'The distance field is required when longitude / latitude is present.',
            ]);
    }

    public function test_radius_must_be_numeric()
    {
        $this->get('posts?distance=A&latitude=B&longitude=C')
            ->assertSessionHasErrors([
                'latitude'  => 'The latitude must be a number.',
                'longitude' => 'The longitude must be a number.',
                'distance'  => 'The distance must be a number.',
            ]);
    }

    public function test_radius_max_distance()
    {
        $this->get('posts?distance=1011&latitude=40.76850772506696&longitude=-73.97186950177363')
            ->assertSessionHasErrors([
                'distance' => 'The distance must be less than or equal to 100.',
            ]);
    }
}
