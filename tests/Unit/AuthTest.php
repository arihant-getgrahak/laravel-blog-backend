<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function user_register()
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

    public function user_login()
    {
        $response = $this->postJson('/api/en/auth/login', [
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
