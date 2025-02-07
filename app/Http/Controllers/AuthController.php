<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    /**
     * The AuthService instance.
     */
    private AuthService $authService;

    /**
     * Constructor to inject AuthService.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        // Assign AuthService instance to the controller property
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Call AuthService to handle registration logic
            $data = $this->authService->register($request->all());
            
            // Return success response with the registered user data
            return $this->successResponse($data, 'User registered successfully.', 201);
        } catch (ValidationException $e) {
            // Return validation error response if validation fails
            return $this->errorResponse($e->errors(), 'Validation failed.', 422);
        } catch (Throwable $e) {
            // Handle unexpected errors and return a generic error response
            return $this->errorResponse($e->getMessage(), 'Registration failed.', 500);
        }
    }

    /**
     * Login an existing user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Call AuthService to handle login logic
            $data = $this->authService->login($request->all());
            
            // Return success response with user authentication data
            return $this->successResponse($data, 'User logged in successfully.');
        } catch (ValidationException $e) {
            // Return validation error response if login validation fails
            return $this->errorResponse($e->errors(), 'Validation failed.', 422);
        } catch (Throwable $e) {
            // Handle unexpected errors and return a generic error response
            return $this->errorResponse($e->getMessage(), 'Login failed.', 500);
        }
    }

    /**
     * Logout the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Get the authenticated user from the request
        $user = $request->user();
        
        // Check if user exists
        if ($user) {
            // Call AuthService to revoke authentication tokens
            $this->authService->logout($user);
            
            // Return success response for successful logout
            return $this->successResponse([], 'User logged out successfully.');
        }
        
        // Return error response if no authenticated user is found
        return $this->errorResponse(null, 'No user found.', 401);
    }

    /**
     * Helper method for success responses.
     *
     * @param array $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    private function successResponse(array $data, string $message, int $statusCode = 200): JsonResponse
    {
        // Format and return JSON success response
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Helper method for error responses.
     *
     * @param mixed $error
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    private function errorResponse(mixed $error, string $message, int $statusCode = 500): JsonResponse
    {
        // Format and return JSON error response
        return response()->json([
            'message' => $message,
            'errors'  => $error,
        ], $statusCode);
    }
}
