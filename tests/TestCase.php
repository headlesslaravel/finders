<?php

namespace HeadlessLaravel\Finders\Tests;

use HeadlessLaravel\Finders\Filter;
use HeadlessLaravel\Finders\Search;
use HeadlessLaravel\Finders\Sort;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Post;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelRay\RayServiceProvider;

class TestCase extends Orchestra
{
    protected $useMysql = false;

    public function setUp(): void
    {
        parent::setUp();

        Route::get('/posts', function () {
            return Post::query()
                ->search([
                    Search::make('title'),
                    Search::make('comments.body'),
                    Search::make('tags.title'),
                ])->sort([
                    Sort::make('title'),
                    Sort::make('comments'),
                    Sort::make('upvotes', 'comments.upvotes'),
                    Sort::make('disliked', 'comments.downvotes'),
                    Sort::make('author_name', 'author.name'),
                ])->filters([
                    Filter::make('id'),
                    Filter::make('author_id')->multiple(),
                    Filter::make('like')->exists()->auth(),
                    Filter::make('length')->range(),
                    Filter::make('author')->relation(),
                    Filter::make('writer', 'author')->relation()->multiple(),
                    Filter::make('active')->boolean(),
                    Filter::make('toggle', 'active')->toggle(),
                    Filter::make('comments')->exists(),
                    Filter::make('comments')->count(),
                    Filter::make('comments')->countRange(),
                    Filter::make('tagged', 'tags')->relation()->multiple(),
                    Filter::make('tags')->exists(),
                    Filter::make('tags')->count(),
                    Filter::make('tags')->countRange(),
                    Filter::make('published_at')->date(),
                    Filter::make('multiple_dates', 'published_at')->date()->multiple(),
                    Filter::make('created_at')->dateRange(),
                    Filter::make('status')->options(['active', 'inactive']),
                    Filter::make('multiple', 'status')->options(['active', 'inactive'])->multiple(),
                    Filter::make('value-scope')->scope('status'),
                    Filter::make('active-scope')->scope('active'),
                    Filter::make('boolean-scope')->scopeBoolean('activeBoolean'),
                    Filter::make('trashed')->trashOnly(),
                    Filter::make('with-trashed')->trashIncluded(),
                    Filter::make('written-by')->search(['author.name']),
                    Filter::make('article-size', 'length')
                        ->when('50', function ($query) {
                            $query->where('length', '50');
                        })->when('100', function ($query) {
                            $query->where('length', '100');
                        }),

                    Filter::make('length-range', 'length')
                        ->between('small', [1, 10])
                        ->between('medium', [11, 20])
                        ->between('large', [21, 30]),

                    Filter::make('length-range', 'length')
                        ->between('small', [1, 10])
                        ->between('medium', [11, 20])
                        ->between('large', [21, 30]),

                    Filter::make('money', 'length')->asCents(),

                    Filter::radius(),

                    Filter::bounds(),
                ])
                ->paginate();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            RayServiceProvider::class,
        ];
    }

    public function useMysql()
    {
        $this->useMysql = true;
    }

    public function getEnvironmentSetUp($app)
    {
        if (!$this->useMysql) {
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);
        }

        include_once __DIR__.'/Fixtures/Database/migrations/create_users_table.php.stub';
        include_once __DIR__.'/Fixtures/Database/migrations/create_posts_table.php.stub';
        include_once __DIR__.'/Fixtures/Database/migrations/create_likes_table.php.stub';
        include_once __DIR__.'/Fixtures/Database/migrations/create_comments_table.php.stub';
        include_once __DIR__.'/Fixtures/Database/migrations/create_categories_table.php.stub';
        include_once __DIR__.'/Fixtures/Database/migrations/create_tags_table.php.stub';

        Schema::dropIfExists('users');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('post_tag');

        (new \CreateUsersTable())->up();
        (new \CreatePostsTable())->up();
        (new \CreateLikesTable())->up();
        (new \CreateCommentsTable())->up();
        (new \CreateTagsTable())->up();
        (new \CreateCategoriesTable())->up();
    }

    public function authUser()
    {
        $user = User::forceCreate([
            'name'        => 'User',
            'email'       => 'user@example.com',
            'password'    => '$2y$10$MTibKZXWRvtO2gWpfpsngOp6FQXWUhHPTF9flhsaPdWvRtsyMUlC2',
            'permissions' => json_encode(['viewAny', 'view', 'create', 'update', 'delete', 'restore', 'forceDelete']),
        ]);

        $this->actingAs($user);

        return $user;
    }
}
