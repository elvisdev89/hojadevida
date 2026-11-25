<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->url = route('v1.me.show');
});

test('an authenticated user can see their account', function () {
    $user = Sanctum::actingAs(User::factory()->create());

    $this->getJson($this->url)
        ->assertOk()
        ->assertExactJson([
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'created_at' => $user->created_at->toJSON(),
            ],
        ]);
});

test('each user only sees their own account', function () {
    User::factory()->create();
    $user = Sanctum::actingAs(User::factory()->create());

    $this->getJson($this->url)
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

test('a guest cannot see an account', function () {
    $this->getJson($this->url)->assertUnauthorized();
});
