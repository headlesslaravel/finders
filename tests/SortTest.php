<?php

namespace HeadlessLaravel\Finders\Tests;

use HeadlessLaravel\Finders\Tests\Fixtures\Models\Comment;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Post;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SortTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sorting_invalid_key()
    {
        $this->get('/posts?sort-desc=invalid')
            ->assertSessionHasErrors();
    }

    public function test_sorting_columns()
    {
        Post::create(['title' => 3]);
        Post::create(['title' => 1]);
        Post::create(['title' => 2]);

        $this->get('/posts?sort-desc=title')
            ->assertJsonPath('data.0.title', '3')
            ->assertJsonPath('data.1.title', '2')
            ->assertJsonPath('data.2.title', '1');

        $this->get('/posts?sort=title')
            ->assertJsonPath('data.0.title', '1')
            ->assertJsonPath('data.1.title', '2')
            ->assertJsonPath('data.2.title', '3');
    }

    public function test_sorting_relationships()
    {
        $threeComments = Post::factory()->has(
            Comment::factory()->count(3)
        )->create();

        $oneComment = Post::factory()->has(
            Comment::factory()->count(1)
        )->create();

        $twoComments = Post::factory()->has(
            Comment::factory()->count(2)
        )->create();

        $this->get('/posts?sort-desc=comments')
            ->assertJsonPath('data.0.id', $threeComments->id)
            ->assertJsonPath('data.1.id', $twoComments->id)
            ->assertJsonPath('data.2.id', $oneComment->id);

        $this->get('/posts?sort=comments')
            ->assertJsonPath('data.0.id', $oneComment->id)
            ->assertJsonPath('data.1.id', $twoComments->id)
            ->assertJsonPath('data.2.id', $threeComments->id);
    }

    public function test_sorting_relationship_columns()
    {
        $twoUpvote = Post::factory()->create();
        $sixUpvote = Post::factory()->create(['title' => 'six upvotes post']);
        $oneUpvote = Post::factory()->create();

        $sixUpvote->comments()->create(['upvotes' => 6]);
        $twoUpvote->comments()->create(['upvotes' => 2]);
        $oneUpvote->comments()->create(['upvotes' => 1]);

        $this->get('/posts?sort-desc=upvotes')
            ->assertJsonPath('data.0.id', $sixUpvote->id)
            ->assertJsonPath('data.1.id', $twoUpvote->id)
            ->assertJsonPath('data.2.id', $oneUpvote->id)
            ->assertJsonPath('data.0.title', 'six upvotes post')
            ->assertJson(['data' => [['upvotes' => 6]]]);

        $this->get('/posts?sort=upvotes')
            ->assertJsonPath('data.0.id', $oneUpvote->id)
            ->assertJsonPath('data.1.id', $twoUpvote->id)
            ->assertJsonPath('data.2.id', $sixUpvote->id)
            ->assertJsonPath('data.2.title', 'six upvotes post')
            ->assertJson(['data' => [['upvotes' => 1]]]);
    }

    public function test_sorting_relationship_column_with_alias()
    {
        $twoDownvote = Post::factory()->create();
        $oneDownvote = Post::factory()->create();
        $sixDownvote = Post::factory()->create();

        $twoDownvote->comments()->create(['downvotes' => 2]);
        $oneDownvote->comments()->create(['downvotes' => 1]);
        $sixDownvote->comments()->create(['downvotes' => 6]);

        $this->get('/posts?sort-desc=disliked')
            ->assertJsonPath('data.0.id', $sixDownvote->id)
            ->assertJsonPath('data.1.id', $twoDownvote->id)
            ->assertJsonPath('data.2.id', $oneDownvote->id);
    }

    public function test_sorting_belongs_to_relationship_values_alphabetically()
    {
        $abby = User::factory()->create(['name' => 'abby']);
        $brad = User::factory()->create(['name' => 'brad']);
        $casey = User::factory()->create(['name' => 'casey']);

        $abbyPost = Post::factory()->create(['author_id' => $abby->id]);
        $bradPost = Post::factory()->create(['author_id' => $brad->id]);
        $caseyPost = Post::factory()->create(['author_id' => $casey->id]);

        $this->get('/posts?sort-desc=author_name')
            ->assertJsonPath('data.2.id', $caseyPost->id)
            ->assertJsonPath('data.1.id', $bradPost->id)
            ->assertJsonPath('data.0.id', $abbyPost->id);

        $this->get('/posts?sort=author_name')
            ->assertJsonPath('data.0.id', $abbyPost->id)
            ->assertJsonPath('data.1.id', $bradPost->id)
            ->assertJsonPath('data.2.id', $caseyPost->id);
    }
}
