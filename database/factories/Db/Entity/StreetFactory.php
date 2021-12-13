<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\City;
use App\Db\Entity\Street;
use Illuminate\Database\Eloquent\Factories\Factory;

class StreetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Street::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'short_name' => $this->faker->name,
        ];
    }
}
