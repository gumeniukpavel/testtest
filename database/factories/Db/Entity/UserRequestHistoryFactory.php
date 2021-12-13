<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\UserRequestHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRequestHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserRequestHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'data' => $this->faker->text,
        ];
    }
}
