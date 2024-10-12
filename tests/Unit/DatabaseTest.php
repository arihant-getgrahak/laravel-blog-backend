<?php

namespace Tests\Unit;

use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_database(): void
    {
        $this->assertDatabaseHas('users', [
            'email' => 'arihant.jain@getgrahak.in',
        ]);
    }
}
