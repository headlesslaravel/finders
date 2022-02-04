<?php

namespace HeadlessLaravel\Finders\Tests\Fixtures\Database\Factories;

use HeadlessLaravel\Finders\Tests\Fixtures\Models\Category;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\Post;
use HeadlessLaravel\Finders\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'       => $this->faker->sentence,
            'body'        => $this->faker->sentence,
            'author_id'   => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
