<?php

namespace BiiiiiigMonster\Clearable\Database\Factories;

use BiiiiiigMonster\Clearable\Tests\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->country
        ];
    }
}
