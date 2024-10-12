<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

// use ServiceProvider;

class MockingTest extends TestCase
{
    use RefreshDatabase;

    public function test_something_can_be_mocked(): void
    {
        Cache::spy();

        $response = $this->get('/api');

        $response->assertStatus(200);

        Cache::shouldReceive('put')->with('name', 'Taylor', 10);
    }
}

// $this->travel(5)->milliseconds();
// $this->travel(5)->seconds();
// $this->travel(5)->minutes();
// $this->travel(5)->hours();
// $this->travel(5)->days();
// $this->travel(5)->weeks();
// $this->travel(5)->years();

// $this->travel(-5)->hours();

// $this->travelTo(now()->subHours(6));

// // Return back to the present time...
// $this->travelBack();
