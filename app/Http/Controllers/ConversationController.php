<?php

namespace App\Http\Controllers;

use App\Interfaces\ConversationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Class ConversationController
 *
 * This controller handles retrieving conversations.
 */
class ConversationController extends Controller
{
    /**
     * @var ConversationServiceInterface $conversationService Service to manage conversations.
     */
    protected $conversationService;

    /**
     * ConversationController constructor.
     *
     * @param ConversationServiceInterface $conversationService Service for handling conversations.
     */
    public function __construct(ConversationServiceInterface $conversationService)
    {
        // Initialize conversation service.
        $this->conversationService = $conversationService;
    }

    /**
     * Retrieves a conversation by its unique identifier.
     *
     * @param string $id Unique identifier of the conversation.
     * @return JsonResponse JSON response containing the conversation data or an error message.
     */
    public function getConversationById(string $id): JsonResponse
    {
        try {
            // Attempt to retrieve the conversation using the service.
            $conversation = $this->conversationService->getConversationById($id);
            
            // Return the conversation data as a JSON response.
            return response()->json($conversation);
        } catch (Exception $e) {
            // Handle any exceptions and return a 404 error response.
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
