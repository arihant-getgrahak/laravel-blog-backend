<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function testBasicExample(): void
    {
        $this->browse(function (Browser $browser) {
            // $time = now();
            $browser->visit('/api')
                ->assertSee(`{"status":"up","message":"Welcome to Blog API","time":"ss"}`);
        });
    }
}
