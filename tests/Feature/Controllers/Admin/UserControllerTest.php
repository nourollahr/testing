<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Middleware\UserActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShowMethod()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware([UserActivity::class]);

        $user = User::factory()->create();

        Cache::shouldReceive('get')
            ->with("user-{$user->id}-status")
            ->once()
            ->andReturn('online');

        $this
            ->actingAs(User::factory()->admin()->create())
            ->get(route('user.show', $user->id))
            ->assertOk()
            ->assertViewIs('admin.user.show')
            ->assertViewHasAll([
                'user' => $user,
                'userStatus' => 'online'
            ]);
    }
}
