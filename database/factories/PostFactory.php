<?php

namespace BiiiiiigMonster\Clearable\Database\Factories;

use BiiiiiigMonster\Clearable\Tests\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'votes' => $this->faker->numberBetween(0, 100),
        ];
    }
}
