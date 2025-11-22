<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class CustomAuthenticateUser
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(Request $request)
    {
        $user = User::where(Fortify::username(), $request->input(Fortify::username()))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            if ($user->is_active) {
                return $user;
            }

            throw ValidationException::withMessages([
                Fortify::username() => [__('This account has been deactivated.')],
            ]);
        }

        throw ValidationException::withMessages([
            Fortify::username() => [__('auth.failed')],
        ]);
    }
}
