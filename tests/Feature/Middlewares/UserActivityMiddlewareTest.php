<?php

namespace Tests\Feature\Middlewares;

use App\Http\Middleware\CheckUserIsAdmin;
use App\Http\Middleware\UserActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserActivityMiddlewareTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanSetUserActivityInCacheWhenUserLoggedIn()
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user);

        $request = Request::create('/', 'GET');

        $middleware = new UserActivity();

        $response = $middleware->handle($request, function(){});

        $this->assertNull($response);
        $this->assertEquals('online', Cache::get("user-{$user->id}-status"));

        $this->travel(11)->second();
        $this->assertNull(Cache::get("user-{$user->id}-status"));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanSetUserActivityInCacheWhenUserNotLoggedIn()
    {
        $request = Request::create('/', 'GET');

        $middleware = new UserActivity();

        $response = $middleware->handle($request, function(){});

        $this->assertNull($response);
    }

    public function testUserActivityMiddlewareSetInWebMiddlewareGroup()
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('home'))
            ->assertOk();

        $this->assertEquals('online', Cache::get("user-{$user->id}-status"));

        $this->assertEquals(\request()->route()->middleware(), ['web']);
    }
}
