<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Tag;

class TagTest extends TestCase
{
    use RefreshDatabase,
        ModelHelperTesting;

    protected function model(): Model
    {
        return new Tag();
    }

    public function testTagRelationshipWithPost()
    {
        $count = rand(1, 10);

        $tag = Tag::factory()
            ->hasPosts($count)
            ->create();

        $this->assertCount($count, $tag->posts);
        $this->assertTrue($tag->posts->first() instanceof Post);
    }
}
