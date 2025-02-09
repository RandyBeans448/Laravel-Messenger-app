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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Conversations
    Route::prefix('conversations')->group(function () {
        Route::get('/{id}', [ConversationController::class, 'getConversationById']);
        Route::post('/', [ConversationController::class, 'createNewConversation']);
    });
    
    // Friend requests
    Route::prefix('friend-requests')->group(function () {
        Route::post('/sendRequest', [FriendRequestController::class, 'sendRequest']);
        Route::post('/resolveFriendRequest', [FriendRequestController::class, 'resolveFriendRequest']);
        Route::get('/getReceivedRequests', [FriendRequestController::class, 'getReceivedRequests']);
    });
    
    // Friends management
    Route::prefix('friends')->group(function () {
        Route::get('/', [FriendController::class, 'getAllFriends']);
        Route::get('/{id}', [FriendController::class, 'getFriend']);
        Route::get('/user/{userId}', [FriendController::class, 'getUserFriends']);
        Route::post('/{friendRequest}/accept', [FriendController::class, 'acceptFriendRequest']);
    });
    
    // Users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/others', [UserController::class, 'getOtherUsers']);
        Route::get('/available', [UserController::class, 'getUsersWithNoPendingRequests'])
            ->name('users.available');
        Route::get('/{id}', [UserController::class, 'show']);
    });

    Route::prefix('chat-room')->group(function () {
        Route::post('/{id}', [ChatController::class, 'sendMessage']);
    });
});