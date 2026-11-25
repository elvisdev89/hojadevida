<?php

use App\Models\User;

beforeEach(function () {
    $this->url = route('v1.auth.logout');
});

test('logging out revokes only the current token', function () {
    $user = User::factory()->create();
    $currentToken = $user->createToken('api')->plainTextToken;
    $otherDeviceToken = $user->createToken('api')->plainTextToken;

    $this->withToken($currentToken)
        ->postJson($this->url)
        ->assertNoContent();

    expect($user->tokens()->count())->toBe(1);

    // The test client reuses the authenticated user between requests;
    // forget it so the next request authenticates from scratch.
    $this->app['auth']->forgetGuards();

    $this->withToken($currentToken)->postJson($this->url)->assertUnauthorized();
    $this->withToken($otherDeviceToken)->postJson($this->url)->assertNoContent();
});

test('logging out requires a token', function () {
    $this->postJson($this->url)
        ->assertUnauthorized()
        ->assertExactJson(['message' => 'Unauthenticated.']);
});

test('an invalid token is rejected', function () {
    $this->withToken('1|not-a-real-token')
        ->postJson($this->url)
        ->assertUnauthorized();
});

test('an expired token is rejected', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;

    $this->travel(config('sanctum.expiration') + 1)->minutes();

    $this->withToken($token)
        ->postJson($this->url)
        ->assertUnauthorized();
});

test('a protected route responds with json 401 even without the accept header', function () {
    $this->post($this->url)
        ->assertUnauthorized()
        ->assertHeader('Content-Type', 'application/json');
});
