<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class LoginPageTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_login_page_loads_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertInputPresent('email')
                ->assertInputPresent('password');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_login_with_invalid_credentials(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'randomemail@gmail.com')
                ->type('password', 'invalid password')
                ->press('Log in')
                ->waitFor('.p-message-error')
                ->assertSee('These credentials do not match our records.');
        });
    }

    public function test_login_with_fresh_user(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'radoslawwalczak63@gmail.com')
                ->type('password', 'kyxdov-5pahno-Tezwub')
                ->press('Log in')
                ->waitForLocation('/');
        });
    }
}
