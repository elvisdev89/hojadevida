<?php

use App\Models\User;

beforeEach(function () {
    $this->url = route('v1.auth.login');
    $this->user = User::factory()->create([
        'email' => 'ana@example.com',
        'password' => 'secret123',
    ]);
});

test('a user can log in with valid credentials', function () {
    $this->postJson($this->url, ['email' => 'ana@example.com', 'password' => 'secret123'])
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'token',
                'user' => ['id', 'email', 'created_at'],
            ],
        ])
        ->assertJsonPath('data.user.id', $this->user->id);

    expect($this->user->tokens()->count())->toBe(1);
});

test('the email is not case sensitive when logging in', function () {
    $this->postJson($this->url, ['email' => 'ANA@Example.com', 'password' => 'secret123'])
        ->assertOk();
});

test('each login creates a new token', function () {
    $credentials = ['email' => 'ana@example.com', 'password' => 'secret123'];

    $first = $this->postJson($this->url, $credentials)->json('data.token');
    $second = $this->postJson($this->url, $credentials)->json('data.token');

    expect($first)->not->toBe($second)
        ->and($this->user->tokens()->count())->toBe(2);
});

test('invalid credentials are rejected with the same generic message', function (string $email, string $password) {
    $this->postJson($this->url, ['email' => $email, 'password' => $password])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'These credentials do not match our records.']);

    expect($this->user->tokens()->count())->toBe(0);
})->with([
    'wrong password' => ['ana@example.com', 'wrong-password1'],
    'unknown email' => ['nobody@example.com', 'secret123'],
]);

test('login validates the input', function (array $input, string $field) {
    $this->postJson($this->url, $input)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'missing email' => [['password' => 'secret123'], 'email'],
    'invalid email' => [['email' => 'not-an-email', 'password' => 'secret123'], 'email'],
    'missing password' => [['email' => 'ana@example.com'], 'password'],
]);
