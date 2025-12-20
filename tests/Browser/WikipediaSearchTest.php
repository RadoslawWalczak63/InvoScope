<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WikipediaSearchTest extends DuskTestCase
{
    private string $url = 'https://wikipedia.org/';

    public function test_search_and_open_page()
    {
        $this->browse(callback: function (Browser $browser) {
            $browser->visit($this->url)
                ->assertVisible('input#searchInput')
                ->type('input#searchInput', 'Selenium (software)')
                ->pause(2000)
                ->keys('input#searchInput', '{enter}')
                ->pause(2000)
                ->assertVisible('#firstHeading')
                ->assertSee('Selenium')
                ->pause(2000)
                ->scrollIntoView('#History')
                ->assertSee('History')
                ->pause(5000);

            $this->url = $browser->driver->getCurrentURL();
        });
    }

    public function test_change_language_to_polish()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit($this->url)
                ->assertVisible('input#searchInput')
                ->type('input#searchInput', 'University Of Bielsko-Biala')
                ->pause(2000)
                ->keys('input#searchInput', '{enter}')
                ->waitFor('#p-lang-btn')
                ->pause(1000)
                ->click('#p-lang-btn')
                ->waitFor('.uls-menu')
                ->pause(1000)
                ->click('a[lang="pl"]')
                ->pause(10000);
        });
    }
}
