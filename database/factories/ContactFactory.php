<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "user_id" => "a286a71c-c92d-431d-9495-a667ccae127e", //User::inRandomOrder()->first()->id,
            "phone" => fake()->phoneNumber(),
            "email" => fake()->email(),
            "address" => fake()->address(),
            "dob" => fake()->date(),
            "notes" => "Some notes about the contact",
            "started" => fake()->boolean()
        ];
    }
}
