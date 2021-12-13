<?php

namespace Database\Factories\Db\Entity;

use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
use App\Db\Entity\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CityCacheSearchItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CityCacheSearchItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [];
    }
}
