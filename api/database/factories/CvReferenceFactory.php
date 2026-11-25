<?php

namespace Database\Factories;

use App\Enums\ReferenceType;
use App\Models\CvReference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CvReference>
 */
class CvReferenceFactory extends Factory
{
    /**
     * Define the model's default state (a professional reference).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'type' => ReferenceType::Professional,
            'company' => fake()->company(),
            'position' => fake()->jobTitle(),
            'phone' => fake()->numerify('3#########'),
            'email' => fake()->optional()->safeEmail(),
        ];
    }

    /**
     * Indicate that the reference is personal (no company or position).
     */
    public function personal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ReferenceType::Personal,
            'company' => null,
            'position' => null,
        ]);
    }
}
