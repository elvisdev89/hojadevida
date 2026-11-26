<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

beforeEach(function () {
    $this->url = route('v1.me.password.update');
    $this->user = User::factory()->create(['password' => 'secret123']);
    $this->token = $this->user->createToken('api')->plainTextToken;
});

function passwordChange(array $overrides = []): array
{
    return [
        'current_password' => 'secret123',
        'password' => 'newSecret456',
        'password_confirmation' => 'newSecret456',
        ...$overrides,
    ];
}

test('a user can change their password', function () {
    $this->withToken($this->token)
        ->putJson($this->url, passwordChange())
        ->assertNoContent();

    expect(Hash::check('newSecret456', $this->user->fresh()->password))->toBeTrue();
});

test('changing the password revokes the other tokens but keeps the current one', function () {
    $otherDeviceToken = $this->user->createToken('api')->plainTextToken;

    $this->withToken($this->token)
        ->putJson($this->url, passwordChange())
        ->assertNoContent();

    expect(PersonalAccessToken::findToken($this->token))->not->toBeNull()
        ->and(PersonalAccessToken::findToken($otherDeviceToken))->toBeNull();
});

test('changing the password does not affect other users tokens', function () {
    $otherUser = User::factory()->create();
    $otherUserToken = $otherUser->createToken('api')->plainTextToken;

    $this->withToken($this->token)
        ->putJson($this->url, passwordChange())
        ->assertNoContent();

    expect(PersonalAccessToken::findToken($otherUserToken))->not->toBeNull();
});

test('the password does not change when the input is invalid', function (array $input, string $field) {
    $this->withToken($this->token)
        ->putJson($this->url, passwordChange($input))
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);

    expect(Hash::check('secret123', $this->user->fresh()->password))->toBeTrue();
})->with([
    'wrong current password' => [['current_password' => 'wrong-password1'], 'current_password'],
    'missing current password' => [['current_password' => null], 'current_password'],
    'missing new password' => [['password' => null], 'password'],
    'new password too short' => [['password' => 'abc12', 'password_confirmation' => 'abc12'], 'password'],
    'new password without numbers' => [['password' => 'onlyletters', 'password_confirmation' => 'onlyletters'], 'password'],
    'new password not confirmed' => [['password_confirmation' => 'different123'], 'password'],
    'new password equal to the current one' => [['password' => 'secret123', 'password_confirmation' => 'secret123'], 'password'],
]);

test('a guest cannot change a password', function () {
    $this->putJson($this->url, passwordChange())->assertUnauthorized();
});
