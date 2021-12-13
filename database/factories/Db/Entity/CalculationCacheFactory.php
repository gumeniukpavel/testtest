<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\CalculationCache;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalculationCacheFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CalculationCache::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'data' => $this->faker->text,
        ];
    }
}
