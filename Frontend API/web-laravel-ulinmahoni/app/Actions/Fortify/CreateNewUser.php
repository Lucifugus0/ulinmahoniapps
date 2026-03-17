<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // If name is not provided or empty, generate it from first_name and last_name
        // This matches the User model's getFullNameAttribute() logic
        $name = !empty($input['name']) ? trim($input['name']) : trim($input['first_name'] . ' ' . $input['last_name']);

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'name' => $name,
            'username' => $input['username'],
            'email' => $input['email'],
            'phone_number' => $input['phone_number'] ?? null,
            'password' => Hash::make($input['password']),
            'status' => 1, // Setting a default status
            'is_admin' => 0, // Default user group (adjust as needed)
        ]);
    }
}
