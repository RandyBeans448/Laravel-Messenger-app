<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Validation\ValidationException;

interface AuthServiceInterface
{
    /**
     * Registers a new user.
     *
     * @param  array  $data
     * @return array  Returns array with user and token info
     * 
     * @throws ValidationException
     */
    public function register(array $data): array;

    /**
     * Login a user and return their token.
     *
     * @param  array  $data
     * @return array
     * 
     * @throws ValidationException
     */
    public function login(array $data): array;

    /**
     * Logout user (revoke all tokens).
     *
     * @param  User  $user
     * @return void
     */
    public function logout(User $user): void;
}
