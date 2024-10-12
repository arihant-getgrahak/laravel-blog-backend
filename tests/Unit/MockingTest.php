<?php

namespace Tests\Unit;

use App\Service;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

// use ServiceProvider;

class MockingTest extends TestCase
{
    public function test_something_can_be_mocked(): void
    {
        $this->instance(
            Service::class,
            Mockery::mock(Service::class, function (MockInterface $mock) {
                $mock->shouldReceive('process')->once();
            })
        );
    }
}
