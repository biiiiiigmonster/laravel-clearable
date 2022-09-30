<?php

namespace BiiiiiigMonster\Clearable\Database\Factories;

use BiiiiiigMonster\Clearable\Tests\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url
        ];
    }
}
