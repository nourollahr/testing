<?php

namespace Tests\Browser\Admin;

use http\Client\Curl\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreatePostTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testCreatePostForm()
    {
        $data = Post::factory()->make();
        $tags = Tag::factory()->count(5)->create();

        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::factory()->admin()->create())
                ->visit(new CreatePostPage)
                ->assertSee('Laravel');
        });
    }
}
