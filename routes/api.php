<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);// working
Route::post('/login', [AuthController::class, 'login']);// working

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);

    // User profile
    Route::get('/user', fn (Request $request) => $request->user());

    
    // Conversations
    Route::prefix('conversations')->group(function () {
        Route::get('/{id}', [ConversationController::class, 'getConversationById']);// working
    });
    
    // Friend requests
    Route::prefix('friend-requests')->group(function () {
        Route::post('/sendRequest', [FriendRequestController::class, 'sendRequest']); // working
        Route::post('/resolveFriendRequest', [FriendRequestController::class, 'resolveFriendRequest']); // working
        Route::get('/getReceivedRequests', [FriendRequestController::class, 'getReceivedRequests']);
    });
    
    // Friends management
    Route::prefix('friends')->group(function () {
        Route::get('/', [FriendController::class, 'getAllFriends']);
        Route::get('/{id}', [FriendController::class, 'getFriend']);
        Route::get('/user/{userId}', [FriendController::class, 'getUserFriends']);
    });
    
    // Users
    Route::prefix('users')->group(function () {

        Route::get('/others', [UserController::class, 'getOtherUsers']);
        Route::get('/available', [UserController::class, 'getUsersWithNoPendingRequests']);
        Route::get('/{id}', [UserController::class, 'getUserById']);
    });

    Route::prefix('chat-room')->group(function () {
        Route::post('/{id}', [ChatController::class, 'sendMessage']); // working
    });
});