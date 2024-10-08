<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_that_true_is_true()
    {
        $response = $this->postJson('/api/auth/register', [
            'email' => 'admin1@admin.com',
            'password' => '123456789',
            'name' => fake('name')->name,
            'type' => 'user',
        ]);

        if ($response->status() == 401) {
            dd($response->json('message'));
        } else {
            $response->assertStatus(200);
            $response->assertJson([
                'status' => $response->json('status'),
                'message' => $response->json('message'),
            ]);
        }
    }

    /** @test */
    public function user_login()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin1@admin.com',
            'password' => '123456789',
        ]);

        if ($response->status() == 401) {
            dd($response->json('message'));
        } else {
            $response->assertStatus(200);
            $response->assertJson([
                'token' => $response->json('token'),
                'message' => $response->json('message'),
            ]);
        }
    }
}
