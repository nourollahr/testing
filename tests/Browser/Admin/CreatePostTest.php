<?php

namespace Tests\Browser\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Tests\Browser\Pages\Admin\CreatePostPage;

class CreatePostTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testCreatePostForm()
    {
        $this->browse(function (Browser $browser) {
            $data = Post::factory()->make();
            $tags = Tag::factory()->count(5)->create();
            $image = UploadedFile::fake()->image('image.png', 100, 100);

            $browser
                ->loginAs(User::factory()->admin()->create())
                ->visit(new CreatePostPage)
                ->type('title', $data->title)
                ->type('description', $data->description)
                ->select(
                    'tags',
                    $tags->take(rand(1,5))->pluck('id')->toArray(),
                )
                ->attach('@postImageInput', $image)
                ->pause(2000)
                ->check('input[type="checkbox"]')
                ->radio('input[type="radio"]', "1")
                ->press('save')
                ->assertRouteIs('post.index');

            $this->assertDatabaseHas(
                'posts',
                Arr::except($data->toArray(), ['user_id', 'image'])
            );
        });
    }
}
