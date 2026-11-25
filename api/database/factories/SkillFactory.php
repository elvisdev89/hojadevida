<?php

namespace Database\Factories;

use App\Enums\SkillCategory;
use App\Enums\SkillLevel;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
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
            'name' => fake()->unique()->word(),
            'category' => fake()->randomElement(SkillCategory::cases()),
            'level' => fake()->randomElement(SkillLevel::cases()),
        ];
    }
}
