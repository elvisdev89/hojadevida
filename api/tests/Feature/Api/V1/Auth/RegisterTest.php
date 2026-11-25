<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

beforeEach(function () {
    $this->url = route('v1.auth.register');
});

function validRegistration(array $overrides = []): array
{
    return [
        'email' => 'ana@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        ...$overrides,
    ];
}

test('a guest can register and receives an api token', function () {
    $response = $this->postJson($this->url, validRegistration());

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'token',
                'user' => ['id', 'email', 'created_at'],
            ],
        ])
        ->assertJsonPath('data.user.email', 'ana@example.com')
        ->assertJsonMissingPath('data.user.password');

    $user = User::sole();
    expect(Hash::check('secret123', $user->password))->toBeTrue()
        ->and($user->tokens()->count())->toBe(1);
});

test('the returned token belongs to the new user', function () {
    $token = $this->postJson($this->url, validRegistration())->json('data.token');

    expect(PersonalAccessToken::findToken($token)?->tokenable->is(User::sole()))->toBeTrue();
});

test('the email is stored trimmed and in lowercase', function () {
    $this->postJson($this->url, validRegistration(['email' => '  Ana@Example.COM ']))
        ->assertCreated();

    $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
});

test('an email cannot be registered twice, regardless of letter case', function () {
    User::factory()->create(['email' => 'ana@example.com']);

    $this->postJson($this->url, validRegistration(['email' => 'ANA@example.com']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'already been taken']);

    expect(User::count())->toBe(1);
});

test('registration validates the input', function (array $input, string $field) {
    $this->postJson($this->url, validRegistration($input))
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);

    expect(User::count())->toBe(0);
})->with([
    'missing email' => [['email' => null], 'email'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'missing password' => [['password' => null], 'password'],
    'password too short' => [['password' => 'abc12', 'password_confirmation' => 'abc12'], 'password'],
    'password without numbers' => [['password' => 'onlyletters', 'password_confirmation' => 'onlyletters'], 'password'],
    'password without letters' => [['password' => '12345678', 'password_confirmation' => '12345678'], 'password'],
    'password not confirmed' => [['password_confirmation' => 'different123'], 'password'],
]);
