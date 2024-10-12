<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConsoleTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_console(): void
    {
        $this->artisan('question')
            ->expectsQuestion('What is your name?', 'Taylor Otwell')
            ->expectsQuestion('Which language do you prefer?', 'PHP')
            ->expectsOutput('Your name is Taylor Otwell and you prefer PHP.')
            ->doesntExpectOutput('Your name is Taylor Otwell and you prefer Ruby.')
            ->assertExitCode(0);
    }
}
