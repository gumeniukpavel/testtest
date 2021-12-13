<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\CompaniesCache;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompaniesCacheFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompaniesCache::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'can_order_now' => $this->faker->boolean,
            'transport_lang' => $this->faker->word,
            'transport_logo' => $this->faker->url,
            'transport_name' => $this->faker->word,
            'transport_number' => $this->faker->numberBetween(1, 200),
            'transport_site' => $this->faker->url,
        ];
    }
}
