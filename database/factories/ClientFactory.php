<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id"            => 1,
            "acquirer_type_code" => "BUSS",
            "name"               => $this->faker->name(),
            "description"        => $this->faker->words(3, true),
            "rfc"                => "UOPG9306079R7",
            "email"              => $this->faker->email(),
            "phone"              => $this->faker->phoneNumber(),
            "location"           => $this->faker->city(),
            "address"            => $this->faker->address(),
            "balance"            => 0,
            "status"             => 1,
            "created_by"         => 1
        ];
    }
}
