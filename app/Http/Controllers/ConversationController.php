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

    public function getConversationById(string $id): JsonResponse
    {
        try {
            $conversation = $this->conversationService->getConversationById($id);
            return response()->json($conversation);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}