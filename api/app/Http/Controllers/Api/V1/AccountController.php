<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DeleteAccountRequest;
use App\Http\Requests\Api\V1\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Show the authenticated user.
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Change the password and log out every other device.
     */
    public function updatePassword(UpdatePasswordRequest $request): Response
    {
        $user = $request->user();

        $user->update(['password' => $request->validated('password')]);

        $user->tokens()
            ->whereKeyNot($user->currentAccessToken()->getKey())
            ->delete();

        return response()->noContent();
    }

    /**
     * Delete the account with all of its data and tokens.
     */
    public function destroy(DeleteAccountRequest $request): Response
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            // personal_access_tokens is polymorphic: it has no foreign key to cascade.
            $user->tokens()->delete();

            // CV tables are removed by their ON DELETE CASCADE foreign keys.
            $user->delete();
        });

        return response()->noContent();
    }
}
