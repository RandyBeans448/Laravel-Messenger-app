<?php

namespace App\Http\Controllers;

use App\Interfaces\ConversationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ConversationController extends Controller
{
    protected $conversationService;

    public function __construct(ConversationServiceInterface $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public function listAllConversations(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $conversations = $this->conversationService->getConversationsForUser($userId);
            return response()->json($conversations);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getConversationById(string $id): JsonResponse
    {
        try {
            $conversation = $this->conversationService->getConversationById($id);
            return response()->json($conversation);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function createNewConversation(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'friends' => 'required|array',
                'friends.*' => 'integer|exists:friends,id' // Adjust validation rules
            ]);

            $conversation = $this->conversationService->createConversation($request->friends);
            return response()->json($conversation, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}