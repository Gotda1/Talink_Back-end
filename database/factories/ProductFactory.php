<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "unit_id"             => 1,
            "product_category_id" => 1,
            "product_type_code"   => "PHYSC",
            "code"                => strtoupper($this->faker->unique()->word(1)),
            "name"                => $this->faker->sentence(3),
            "description"         => $this->faker->sentence(6),
            "price_list"          => $this->faker->randomNumber(3),
            "flec_price"          => 1,
            "status"              => 1,
            "created_by"          => 0
        ];
    }
}
