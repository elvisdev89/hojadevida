<?php

namespace Database\Factories;

use App\Enums\EducationLevel;
use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
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
            'institution' => fake()->company(),
            'title' => fake()->jobTitle(),
            'level' => fake()->randomElement(EducationLevel::cases()),
            'start_date' => fake()->dateTimeBetween('-15 years', '-6 years'),
            'end_date' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'is_current' => false,
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the user is currently studying.
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => null,
            'is_current' => true,
        ]);
    }
}
