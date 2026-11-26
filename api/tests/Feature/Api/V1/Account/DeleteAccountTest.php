<?php

use App\Models\Language;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

beforeEach(function () {
    $this->url = route('v1.me.destroy');
    $this->user = User::factory()
        ->has(Profile::factory())
        ->has(Skill::factory()->count(2))
        ->create(['password' => 'secret123']);
    $this->token = $this->user->createToken('api')->plainTextToken;
});

test('a user can delete their account with all of its data and tokens', function () {
    $this->user->createToken('api');
    $language = Language::factory()->create();
    $this->user->languages()->attach($language, ['level' => 'B2']);

    $this->withToken($this->token)
        ->deleteJson($this->url, ['password' => 'secret123'])
        ->assertNoContent();

    $this->assertModelMissing($this->user);
    $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $this->user->id]);
    $this->assertDatabaseMissing('profiles', ['user_id' => $this->user->id]);
    $this->assertDatabaseMissing('skills', ['user_id' => $this->user->id]);
    $this->assertDatabaseMissing('language_user', ['user_id' => $this->user->id]);
    $this->assertModelExists($language);
});

test('the token stops working after the account is deleted', function () {
    $this->withToken($this->token)
        ->deleteJson($this->url, ['password' => 'secret123'])
        ->assertNoContent();

    $this->app['auth']->forgetGuards();

    $this->withToken($this->token)
        ->getJson(route('v1.me.show'))
        ->assertUnauthorized();
});

test('deleting an account does not affect other users', function () {
    $otherUser = User::factory()->has(Skill::factory())->create();
    $otherUserToken = $otherUser->createToken('api')->plainTextToken;

    $this->withToken($this->token)
        ->deleteJson($this->url, ['password' => 'secret123'])
        ->assertNoContent();

    $this->assertModelExists($otherUser);
    expect($otherUser->skills()->count())->toBe(1)
        ->and(PersonalAccessToken::findToken($otherUserToken))->not->toBeNull();
});

test('the account is not deleted without the correct password', function (array $input) {
    $this->withToken($this->token)
        ->deleteJson($this->url, $input)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('password');

    $this->assertModelExists($this->user);
    expect($this->user->skills()->count())->toBe(2)
        ->and($this->user->tokens()->count())->toBe(1);
})->with([
    'wrong password' => [['password' => 'wrong-password1']],
    'missing password' => [[]],
]);

test('a guest cannot delete an account', function () {
    $this->deleteJson($this->url, ['password' => 'secret123'])->assertUnauthorized();

    $this->assertModelExists($this->user);
});
