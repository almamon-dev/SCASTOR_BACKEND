<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Services\AiChatService;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    protected $chatService;

    public function __construct(AiChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Start/Resume a chat session.
     */
    public function startChat(Request $request)
    {
        $user = $request->user();
        $sessionId = 'user_' . $user->id;

        // Check if messages exist for this user
        $exists = AiChatMessage::where('user_id', $user->id)->exists();

        if (!$exists) {
            $welcomeMsg = "Hi! I'm your CASTORS AI Chef. Tell me what ingredients you have, and I'll create a delicious recipe using CASTORS seasoning.";

            AiChatMessage::create([
                'user_id' => $user->id,
                'user_message' => 'START_SESSION',
                'ai_response' => $welcomeMsg,
                'session_id' => $sessionId,
            ]);
        }

        return $this->getHistory($request);
    }

    /**
     * Handle chat message from user
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = $request->user();
        $message = $request->message;
        $sessionId = 'user_' . $user->id;

        // Get recent history for context (last 5 interactions)
        $history = AiChatMessage::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->latest()
            ->take(5)
            ->get()
            ->reverse()
            ->map(function ($chat) {
                return [
                    ['role' => 'user', 'content' => $chat->user_message],
                    ['role' => 'assistant', 'content' => $chat->ai_response],
                ];
            })
            ->flatten(1)
            ->toArray();

        // Call AI Service
        $aiResponse = $this->chatService->sendMessage($message, $history);

        if (!$aiResponse) {
            return response()->json([
                'success' => false,
                'message' => 'AI failed to respond. Please try again later.',
            ], 500);
        }

        // Store in Database
        $chat = AiChatMessage::create([
            'user_id' => $user->id,
            'user_message' => $message,
            'ai_response' => $aiResponse,
            'session_id' => $sessionId,
        ]);

        return response()->json([
            'success' => true,
            'data' => $chat,
            'message' => 'Message sent successfully.',
        ]);
    }

    /**
     * Get chat history for the authenticated user
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();

        $history = AiChatMessage::where('user_id', $user->id)
            ->latest()
            ->take(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}
