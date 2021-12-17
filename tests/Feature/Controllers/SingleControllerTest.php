<?php

namespace Tests\Feature\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SingleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndexMethod()
    {
//        $this->withoutExceptionHandling();
        $post = Post::factory()->hasComments(rand(0, 3))->create();

        $response = $this->get(route('single', $post->id));

        $response->assertOk();
        $response->assertViewIs('single');
        $response->assertViewHasAll([
            'post' => $post,
            'comments' => $post->comments()->latest()->paginate(15)
        ]);
    }

    public function testCommentMethodWhenUserLoggedIn()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $data = Comment::factory()->state([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
        ])->make()->toArray();

        $response = $this
            ->actingAs($user)
            ->withHeaders([
                'HTTP_X-Requested-with' => 'XMLHttpRequest'
            ])
            ->postJson(
                route('single.comment', $post->id),
                ['text' => $data['text']]
            );

        $response
            ->assertOk()
            ->assertJson([
                'created' => true
            ]);
        $this->assertDatabaseHas('comments', $data);
    }

    public function testCommentMethodWhenUserNotLoggedIn()
    {
//        $this->withoutExceptionHandling();
        $post = Post::factory()->create();

        $data = Comment::factory()->state([
            'commentable_id' => $post->id,
        ])->make()->toArray();

        unset($data['user_id']);

        $response = $this
            ->withHeaders([
                'HTTP_X-Requested-with' => 'XMLHttpRequest'
            ])
            ->postJson(
                route('single.comment', $post->id),
                ['text' => $data['text']]
            );

        $response->assertUnauthorized();
        $this->assertDatabaseMissing('comments', $data);
    }

    public function testCommentMethodValidRequiredData()
    {
//        $this->withoutExceptionHandling();
        $post = Post::factory()->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->withHeaders([
                'HTTP_X-Requested-with' => 'XMLHttpRequest'
            ])
            ->postJson(
                route('single.comment', $post->id),
                ['text' => '']
            );

        $response->assertJsonValidationErrors([
            'text' => 'The text field is required.'
        ]);
    }
}
