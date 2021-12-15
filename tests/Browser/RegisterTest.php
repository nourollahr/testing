<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testRegisterForm()
    {
        $user = User::factory()->make();

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->visitRoute('register')
                ->type('name', $user->name)
                ->type('email', $user->email)
                ->type('password', $user->password)
                ->typeSlowly('password_confirmation', $user->password)
                ->click('form button[type="submit"]')
                ->assertSee('Home Page')
                ->assertAuthenticatedAs(User::whereEmail($user->email)->first())
                ->assertPathIs('/');

        });
    }
}
