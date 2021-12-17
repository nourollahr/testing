<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\RegisterPage;
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

        $this->browse(function (Browser $browser) use($user) {
            $browser
                ->visit(new RegisterPage)
                ->type('name', $user->name)
                ->type('email', $user->email)
                ->type('password', $user->password)
                ->type('password_confirmation', $user->password)
                ->click('@submitButton')
                ->screenshot('screenshot')
                ->storeConsoleLog('log')
                ->storeSource('source')
                ->assertSee('Home Page')
                ->assertAuthenticatedAs(User::whereEmail($user->email)->first())
                ->assertPathIs('/');
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testRegisterFormValidation()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit(new RegisterPage)
                ->submitForm()
                ->assertSeeIn(
                    'input[name="name"] ~ .invalid-feedback',
                    'The name field is required.')
                ->assertSeeIn(
                    'input[name="email"] ~ .invalid-feedback',
                    'The email field is required.')
                ->assertSeeIn(
                    'input[name="password"] ~ .invalid-feedback',
                    'The password field is required.')
                ->assertPathIs('/register');

            $data = User::factory()->make(['email' => 'dasfsa'])->toArray();
            $browser
                ->submitForm($data)
                ->assertSeeIn(
                    'input[name="email"] ~ .invalid-feedback',
                    'The email must be a valid email address.');
        });
    }
}
