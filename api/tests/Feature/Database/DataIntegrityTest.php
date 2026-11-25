<?php

use App\Models\Course;
use App\Models\CvReference;
use App\Models\Education;
use App\Models\Language;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use App\Models\WorkExperience;
use Illuminate\Database\QueryException;

test('deleting a user deletes all of their CV data', function () {
    $user = User::factory()
        ->has(Profile::factory())
        ->has(Education::factory()->count(2), 'educations')
        ->has(WorkExperience::factory()->count(2))
        ->has(CvReference::factory()->count(2))
        ->has(Skill::factory()->count(3))
        ->has(Course::factory()->count(2))
        ->create();
    $language = Language::factory()->create();
    $user->languages()->attach($language, ['level' => 'B2']);

    $user->delete();

    expect(Profile::count())->toBe(0)
        ->and(Education::count())->toBe(0)
        ->and(WorkExperience::count())->toBe(0)
        ->and(CvReference::count())->toBe(0)
        ->and(Skill::count())->toBe(0)
        ->and(Course::count())->toBe(0);
    $this->assertDatabaseMissing('language_user', ['user_id' => $user->id]);
    $this->assertModelExists($language);
});

test('deleting a user does not delete other users data', function () {
    $user = User::factory()->has(Skill::factory())->create();
    $otherUser = User::factory()->has(Skill::factory())->create();

    $user->delete();

    expect($otherUser->skills()->count())->toBe(1);
});

test('a user cannot have two profiles', function () {
    $user = User::factory()->has(Profile::factory())->create();

    Profile::factory()->for($user)->create();
})->throws(QueryException::class, 'Duplicate entry');

test('two profiles cannot share the same identification document', function () {
    Profile::factory()->create(['identification_type' => 'CC', 'identification_number' => '1020304050']);

    Profile::factory()->create(['identification_type' => 'CC', 'identification_number' => '1020304050']);
})->throws(QueryException::class, 'Duplicate entry');

test('a user cannot repeat a skill, regardless of letter case', function () {
    $user = User::factory()->create();
    Skill::factory()->for($user)->create(['name' => 'PHP']);

    Skill::factory()->for($user)->create(['name' => 'php']);
})->throws(QueryException::class, 'Duplicate entry');

test('different users can have the same skill', function () {
    Skill::factory()->create(['name' => 'PHP']);
    Skill::factory()->create(['name' => 'PHP']);

    expect(Skill::where('name', 'PHP')->count())->toBe(2);
});

test('a user cannot register the same language twice', function () {
    $user = User::factory()->create();
    $language = Language::factory()->create();
    $user->languages()->attach($language, ['level' => 'B1']);

    $user->languages()->attach($language, ['level' => 'C1']);
})->throws(QueryException::class, 'Duplicate entry');

test('a language in use cannot be deleted from the catalog', function () {
    $user = User::factory()->create();
    $language = Language::factory()->create();
    $user->languages()->attach($language, ['level' => 'B1']);

    $language->delete();
})->throws(QueryException::class, 'foreign key constraint fails');
