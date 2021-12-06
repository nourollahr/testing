<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $middlewares = ['web', 'admin'];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
        Post::factory()->count(100)->create();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->get(route('post.index'))
            ->assertOk()
            ->assertViewIs('admin.post.index')
            ->assertViewHas('posts', Post::latest()->paginate(15));

        $this->assertEquals(
             request()->route()->middleware(),
             $this->middlewares
        );
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateMethod()
    {
        Tag::factory()->count(20)->create();

        $this
            ->actingAs(User::factory()->admin()->create())
            ->get(route('post.create'))
            ->assertOk()
            ->assertViewIs('admin.post.create')
            ->assertViewHas('tags', Tag::latest()->get());

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
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

        $this
            ->actingAs(User::factory()->admin()->create())
            ->get(route('post.edit', $post->id))
            ->assertOk()
            ->assertViewIs('admin.post.edit')
            ->assertViewHasAll([
                'tags' => Tag::latest()->get(),
                'post' => $post
            ]);

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testStoreMethod()
    {
        $user = User::factory()->admin()->create();
        $tags = Tag::factory()->count(rand(1,5))->create();
        $data = Post::factory()
            ->state(['user_id' => $user->id])->make()->toArray();

        $this
              ->actingAs($user)
              ->post(route('post.store'),
                  array_merge($data, ['tags' => $tags->pluck('id')->toArray()])
              )
              ->assertSessionHas('message', 'new post has been created')
              ->assertRedirect(route('post.index'));

          $this->assertDatabaseHas('posts', $data);

          $this->assertEquals(
              $tags->pluck('id')->toArray(),
              Post::where($data)->first()->tags()->pluck('id')->toArray()
          );

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUpdateMethod()
    {
        $user = User::factory()->admin()->create();
        $tags = Tag::factory()->count(rand(1,5))->create();
        $data = Post::factory()
            ->state(['user_id' => $user->id])->make()->toArray();

        $post = Post::factory()
            ->state(['user_id' => $user->id])
            ->hasTags(rand(1,5))
            ->create();

        $this
            ->actingAs($user)
            ->patch(route('post.update', $post->id),
                array_merge($data, ['tags' => $tags->pluck('id')->toArray()])
            )
            ->assertSessionHas('message', 'the  post has been updated')
            ->assertRedirect(route('post.index'));

        $this->assertDatabaseHas('posts', array_merge(['id' => $post->id], $data));

        $this->assertEquals(
            $tags->pluck('id')->toArray(),
            Post::where($data)->first()->tags()->pluck('id')->toArray()
        );

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testValidationRequestRequiredData()
    {
        $user = User::factory()->admin()->create();
        $errors = [
            'title' =>  'The title field is required.',
            'description' => 'The description field is required.',
            'image' => 'The image field is required.',
            'tags' => 'The tags field is required.'
        ];

        // method store
        $this->actingAs($user)
            ->post(route('post.store'), [])
            ->assertSessionHasErrors($errors);

        // method update
        $this->actingAs($user)
            ->post(route('post.update', Post::factory()->create()->id), [])
            ->assertSessionHasErrors($errors);
    }

    public function testValidationRequestDescriptionDataHasMinimumRules()
    {
        $user = User::factory()->admin()->create();
        $data = ['description' => 'lord'];
        $errors = [
            'description' => 'The description must be at least 5 characters.',
        ];

        // method store
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        // method update
        $this->actingAs($user)
            ->post(route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationRequestImageDataHasUrlRules()
    {
        $user = User::factory()->admin()->create();
        $data = ['image' => 'lord'];
        $errors = [
            'image' => 'The image must be a valid URL.',
        ];

        // method store
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        // method update
        $this->actingAs($user)
            ->post(route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }


    public function testValidationRequestTagsDataHasArrayRules()
    {
        $user = User::factory()->admin()->create();
        $data = ['tags' => 'lord'];
        $errors = [
            'tags' => 'The tags must be an array.',
        ];

        // method store
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        // method update
        $this->actingAs($user)
            ->post(route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }

    public function testValidationRequestTagsDataMustExistsInTagsTable()
    {
        $user = User::factory()->admin()->create();
        $data = ['tags' => [0]];
        $errors = [
            'tags.0' => 'The selected tags.0 is invalid.',
        ];

        // method store
        $this->actingAs($user)
            ->post(route('post.store'), $data)
            ->assertSessionHasErrors($errors);

        // method update
        $this->actingAs($user)
            ->post(route('post.update', Post::factory()->create()->id), $data)
            ->assertSessionHasErrors($errors);
    }
}
