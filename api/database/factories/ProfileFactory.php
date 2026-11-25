<?php

namespace Database\Factories;

use App\Enums\IdentificationType;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'identification_type' => fake()->randomElement(IdentificationType::cases()),
            'identification_number' => fake()->unique()->numerify('##########'),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'phone' => fake()->numerify('3#########'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'summary' => fake()->paragraph(),
        ];
    }
}
