<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state (a certification that does not expire).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->catchPhrase(),
            'institution' => fake()->company(),
            'completed_at' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'expires_at' => null,
        ];
    }
}
