<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            // $time = now();
            $browser->visit('/api')
                ->assertSee(`{"status":"up","message":"Welcome to Blog API","time":"ss"}`);
        });
    }
}
