<?php

namespace Tests\Browser;

// use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BrowserTest extends DuskTestCase
{
    // use DatabaseTruncation;

    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            // $time = now();
            $browser->visit('/test')
                ->assertSee('Laravel');
            // ->waitForText('{"status":"up","message":"Welcome to Blog API","time":"2024-10-12T09:49:09.974247Z"}',50);
        });
    }
}
