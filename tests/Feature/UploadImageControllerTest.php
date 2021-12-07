<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadImageControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUploadMethodCanUploadImage()
    {
        $image = UploadedFile::fake()->image('image.png');

        $this
            ->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X-Requested-with' => 'XMLHttpRequest',
            ])
            ->postJson(route('upload'), compact('image'))
            ->assertOk()
            ->assertJson(['url' => "/upload/{$image->hashName()}"]);

        $this->assertFileExists(public_path("upload/{$image->hashName()}"));

        $this->assertEquals(
            request()->route()->middleware(),
            ['web', 'admin']
        );
    }
}
