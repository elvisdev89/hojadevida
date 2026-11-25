<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkExperience>
 */
class WorkExperienceFactory extends Factory
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
            'company' => fake()->company(),
            'position' => fake()->jobTitle(),
            'start_date' => fake()->dateTimeBetween('-15 years', '-6 years'),
            'end_date' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'is_current' => false,
            'responsibilities' => fake()->paragraph(),
            'achievements' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the user currently works here.
     */
    public function current(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => null,
            'is_current' => true,
        ]);
    }
}
