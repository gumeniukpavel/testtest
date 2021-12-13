<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\CompaniesCacheName;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompaniesCacheNameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompaniesCacheName::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'lang' => $this->faker->word,
            'name' => $this->faker->name,
        ];
    }
}
