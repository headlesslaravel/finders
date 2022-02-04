<?php

namespace HeadlessLaravel\Finders\Tests;

use HeadlessLaravel\Finders\Tests\Fixtures\Models\Comment;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Post;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_search()
    {
        Post::factory(3)->create();

        $expected = Post::create(['title' => 'hello world']);

        $this->get('/posts?search=world')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $expected->id);
    }

    public function test_search_relations()
    {
        $rejected = Post::factory()->create();
        $expected = Post::factory()->create();

        Comment::factory()->create([
            'body'    => 'Laravel is an amazing framework',
            'post_id' => $expected->id,
        ]);

        $this->get('/posts?search=amazing')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $expected->id);
    }

    public function test_search_relations_with_pivots()
    {
        $tag1 = Tag::create(['title' => 'PHP']);
        $tag2 = Tag::create(['title' => 'JS']);

        $rejected = Post::factory()->create();
        $expected = Post::factory()->create();

        $rejected->tags()->attach([$tag2->id]);
        $expected->tags()->attach([$tag1->id]);

        $this->get('/posts?search=php')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $expected->id);
    }
}
