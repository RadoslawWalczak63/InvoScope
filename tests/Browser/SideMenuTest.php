<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class SideMenuTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_sidebar_elements_are_visible()
    {
        $user = User::first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->waitForText(config('app.name'), ignoreCase: true)
                ->assertSee('Dashboard')
                ->assertSee('Entities')
                ->assertSee('Invoices')
                ->assertSee('Queued Jobs')
                ->assertSee($user->name)
                ->assertSee($user->email);
        });
    }

    /**
     * @throws Throwable
     */
    public function test_click_each_sidebar_link()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                ->visit('/')
                ->waitForText(config('app.name'), ignoreCase: true)
                ->clickLink('Entities')
                ->waitForRoute('entities.index')
                ->clickLink('Invoices')
                ->waitForLocation('/invoices')
                ->waitForRoute('invoices.index')
                ->clickLink('Queued Jobs')
                ->waitForRoute('queued-jobs.index');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_open_profile_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                ->visit('/')
                ->click('@user-menu-button')
                ->clickLink('Profile')
                ->waitForRoute('profile.edit');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_logout()
    {
        $user = User::find(1);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::first())
                ->visit('/')
                ->click('@user-menu-button')
                ->clickLink('Logout')
                ->waitForLocation('/login')
                ->assertGuest();
        });
    }
}
