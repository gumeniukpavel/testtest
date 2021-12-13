<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheOption;
use App\Db\Entity\CompaniesCachePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompaniesCachePaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompaniesCachePayment::class;

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
