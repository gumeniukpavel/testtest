<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_name' => $this->faker->name,
            'client_email' => $this->faker->email,
            'unique_identity_number' => $this->faker->numberBetween(1000000, 9999999),
            'notes' => $this->faker->text,
        ];
    }
}
