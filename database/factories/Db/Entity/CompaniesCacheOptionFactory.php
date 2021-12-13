<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompaniesCacheOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompaniesCacheOption::class;

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
