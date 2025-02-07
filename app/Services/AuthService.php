<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Interfaces\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    /**
     * Registers a new user and issues an access token.
     *
     * @param  array  $data
     * @return array
     * @throws ValidationException
     */
    public function register(array $data): array
    {
        // Validate registration data
        $validatedData = $this->validateRegisterData($data);
        
        // Create a new user with validated data
        $user = User::create([
            'name'     => $validatedData['name'], // Assign user's name
            'email'    => $validatedData['email'], // Assign user's email
            'password' => Hash::make($validatedData['password']), // Hash the password before saving
        ]);
        
        // Generate and return authentication token
        return $this->generateTokenResponse($user);
    }

    /**
     * Logs in a user and returns an access token.
     *
     * @param  array  $data
     * @return array
     * @throws ValidationException
     */
    public function login(array $data): array
    {
        // Validate login data
        $validatedData = $this->validateLoginData($data);
        
        // Find the user by email
        $user = User::where('email', $validatedData['email'])->first();

        // Check if user exists and verify password
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Generate and return authentication token
        return $this->generateTokenResponse($user);
    }

    /**
     * Logs out the user by revoking all tokens.
     *
     * @param  User  $user
     * @return void
     */
    public function logout(User $user): void
    {
        // Revoke all authentication tokens for the user
        $user->tokens()->delete();
    }

    /**
     * Validates user registration data.
     *
     * @param  array  $data
     * @return array
     * @throws ValidationException
     */
    private function validateRegisterData(array $data): array
    {
        // Define validation rules for registration
        $validator = Validator::make($data, [
            'name'     => 'required|string|max:255', // Name is required, must be a string, max length 255
            'email'    => 'required|string|email|max:255|unique:users', // Email is required, must be unique
            'password' => 'required|string|min:8|confirmed', // Password must be at least 8 characters and confirmed
        ]);
        
        // Throw an exception if validation fails
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Return validated data
        return $validator->validated();
    }

    /**
     * Validates user login data.
     *
     * @param  array  $data
     * @return array
     * @throws ValidationException
     */
    private function validateLoginData(array $data): array
    {
        // Define validation rules for login
        $validator = Validator::make($data, [
            'email'    => 'required|string|email', // Email is required and must be a valid email
            'password' => 'required|string', // Password is required
        ]);

        // Throw an exception if validation fails
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Return validated data
        return $validator->validated();
    }

    /**
     * Generates an authentication token response for a user.
     *
     * @param  User  $user
     * @return array
     */
    private function generateTokenResponse(User $user): array
    {
        return [
            'user'         => $user, // Return user details
            'access_token' => $user->createToken('auth_token')->plainTextToken, // Generate new authentication token
            'token_type'   => 'Bearer', // Specify token type as Bearer
        ];
    }
}
