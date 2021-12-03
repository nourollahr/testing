<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
        Post::factory()->count(100)->create();

        $this->get(route('post.index'))
            ->assertOk()
            ->assertViewIs('admin.post.index')
            ->assertViewHas('posts', Post::latest()->paginate(15));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateMethod()
    {
        Tag::factory()->count(20)->create();

        $this->get(route('post.create'))
            ->assertOk()
            ->assertViewIs('admin.post.create')
            ->assertViewHas('tags', Tag::latest()->get());
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testEditMethod()
    {
        $post = Post::factory()->create();
        Tag::factory()->count(20)->create();

        $this->get(route('post.edit', $post->id))
            ->assertOk()
            ->assertViewIs('admin.post.edit')
            ->assertViewHasAll([
                'tags' => Tag::latest()->get(),
                'post' => $post
            ]);
    }
}
