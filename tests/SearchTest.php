<?php

namespace HeadlessLaravel\Finders\Tests;

use HeadlessLaravel\Finders\Exceptions\ReservedException;
use HeadlessLaravel\Finders\Exceptions\UnauthorizedException;
use HeadlessLaravel\Finders\Filter;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Comment;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Like;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Post;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Tag;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\User;
use HeadlessLaravel\Finders\Tests\Fixtures\PostFormation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class SearchTest extends TestCase
{
    use RefreshDatabase;

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
