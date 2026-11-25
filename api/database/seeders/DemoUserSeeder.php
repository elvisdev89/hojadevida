<?php

namespace Database\Seeders;

use App\Enums\LanguageLevel;
use App\Models\Course;
use App\Models\CvReference;
use App\Models\Education;
use App\Models\Language;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Database\Seeder;

/**
 * Demo account with a complete CV, for local development only.
 *
 * Requires the language catalog (LanguageSeeder) to be seeded first.
 */
class DemoUserSeeder extends Seeder
{
    public const EMAIL = 'demo@example.com';

    public const PASSWORD = 'password';

    public function run(): void
    {
        if (User::where('email', self::EMAIL)->exists()) {
            return;
        }

        $user = User::factory()
            ->has(Profile::factory())
            ->has(Education::factory()->count(2), 'educations')
            ->has(Education::factory()->current(), 'educations')
            ->has(WorkExperience::factory()->count(2))
            ->has(WorkExperience::factory()->current())
            ->has(CvReference::factory()->count(2))
            ->has(CvReference::factory()->personal())
            ->has(Skill::factory()->count(5))
            ->has(Course::factory()->count(2))
            ->create([
                'email' => self::EMAIL,
                'password' => self::PASSWORD,
            ]);

        $languages = Language::whereIn('code', ['es', 'en'])->pluck('id', 'code');

        // Every row must have the same keys: attach() builds a single INSERT
        // using the columns of the first row.
        $user->languages()->attach([
            $languages['es'] => ['level' => LanguageLevel::Native->value, 'certification' => null],
            $languages['en'] => ['level' => LanguageLevel::B2->value, 'certification' => 'IELTS'],
        ]);
    }
}
