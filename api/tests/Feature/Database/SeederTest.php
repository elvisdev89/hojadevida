<?php

use App\Models\Language;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoUserSeeder;
use Database\Seeders\LanguageSeeder;

test('the language seeder can run several times without duplicating languages', function () {
    $this->seed(LanguageSeeder::class);
    $count = Language::count();

    $this->seed(LanguageSeeder::class);

    expect($count)->toBeGreaterThan(0)
        ->and(Language::count())->toBe($count);
});

test('the database seeder does not create the demo user outside the local environment', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Language::count())->toBeGreaterThan(0)
        ->and(User::count())->toBe(0);
});

test('the demo user seeder creates a complete CV and can run several times', function () {
    $this->seed([LanguageSeeder::class, DemoUserSeeder::class]);
    $this->seed(DemoUserSeeder::class);

    $user = User::where('email', DemoUserSeeder::EMAIL)->sole();

    expect($user->profile)->not->toBeNull()
        ->and($user->educations)->toHaveCount(3)
        ->and($user->workExperiences)->toHaveCount(3)
        ->and($user->cvReferences)->toHaveCount(3)
        ->and($user->skills)->toHaveCount(5)
        ->and($user->courses)->toHaveCount(2)
        ->and($user->languages)->toHaveCount(2);
});
