<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('login is blocked after 5 attempts per minute for the same email and IP', function () {
    $credentials = ['email' => 'ana@example.com', 'password' => 'wrong-password1'];

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->postJson(route('v1.auth.login'), $credentials)->assertUnprocessable();
    }

    $this->postJson(route('v1.auth.login'), $credentials)
        ->assertTooManyRequests()
        ->assertHeader('Retry-After')
        ->assertJsonStructure(['message']);
});

test('the login limit ignores letter case and spaces in the email', function () {
    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->postJson(route('v1.auth.login'), ['email' => 'ana@example.com', 'password' => 'wrong-password1']);
    }

    $this->postJson(route('v1.auth.login'), ['email' => ' ANA@example.com ', 'password' => 'wrong-password1'])
        ->assertTooManyRequests();
});

test('a blocked email does not block other accounts', function () {
    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->postJson(route('v1.auth.login'), ['email' => 'ana@example.com', 'password' => 'wrong-password1']);
    }

    User::factory()->create(['email' => 'luis@example.com', 'password' => 'secret123']);

    $this->postJson(route('v1.auth.login'), ['email' => 'luis@example.com', 'password' => 'secret123'])
        ->assertOk();
});

test('the login limit is released after one minute', function () {
    User::factory()->create(['email' => 'ana@example.com', 'password' => 'secret123']);

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->postJson(route('v1.auth.login'), ['email' => 'ana@example.com', 'password' => 'wrong-password1']);
    }

    $this->travel(61)->seconds();

    $this->postJson(route('v1.auth.login'), ['email' => 'ana@example.com', 'password' => 'secret123'])
        ->assertOk();
});

test('registration is blocked after 5 attempts per minute from the same IP', function () {
    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->postJson(route('v1.auth.register'), [
            'email' => "user{$attempt}@example.com",
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertCreated();
    }

    $this->postJson(route('v1.auth.register'), [
        'email' => 'user6@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ])->assertTooManyRequests();

    expect(User::count())->toBe(5);
});

test('api routes are limited to 60 requests per minute', function () {
    for ($request = 1; $request <= 60; $request++) {
        $this->getJson(route('v1.languages.index'))->assertOk();
    }

    $this->getJson(route('v1.languages.index'))->assertTooManyRequests();
});

test('the api limit is counted per user, not shared between users', function () {
    Sanctum::actingAs(User::factory()->create());
    for ($request = 1; $request <= 60; $request++) {
        $this->getJson(route('v1.me.show'));
    }
    $this->getJson(route('v1.me.show'))->assertTooManyRequests();

    Sanctum::actingAs(User::factory()->create());
    $this->getJson(route('v1.me.show'))->assertOk();
});
