<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Services\AiChatService; // Updated Service
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    protected $chatService;

    public function __construct(AiChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Start/Resume a chat session. Auto-sends welcome message if new.
     */
    public function startChat(Request $request)
    {
        $user = $request->user();
        $sessionId = $request->session_id ?? ($user ? 'user_'.$user->id : uniqid('guest_'));

        // Check if messages exist for this session
        $exists = AiChatMessage::where('session_id', $sessionId)->exists();

        if (! $exists) {
            // Auto-send welcome message
            $welcomeMsg = "Hi! I'm your CASTORS AI Chef. Tell me what ingredients you have, and I'll create a delicious recipe using CASTORS seasoning.";

            AiChatMessage::create([
                'user_id' => $user ? $user->id : null,
                'user_message' => 'START_SESSION', // Internal flag
                'ai_response' => $welcomeMsg,
                'session_id' => $sessionId,
            ]);
        }

        // Return latest history
        return $this->getHistory($request, $sessionId);
    }

    /**
     * Handle chat message from user
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'session_id' => 'required|string', // Enforce session ID for context
        ]);

        $user = $request->user();
        $message = $request->message;
        $sessionId = $request->session_id;

        // Get recent history for context (last 5 interactions)
        $history = AiChatMessage::where('session_id', $sessionId)
            ->latest()
            ->take(5)
            ->get()
            ->reverse()
            ->map(function ($chat) {
                // Map to OpenAI message format
                return [
                    ['role' => 'user', 'content' => $chat->user_message],
                    ['role' => 'assistant', 'content' => $chat->ai_response],
                ];
            })
            ->flatten(1)
            ->toArray();

        // Call AI Service with history
        $aiResponse = $this->chatService->sendMessage($message, $history);

        if (! $aiResponse) {
            return response()->json([
                'success' => false,
                'message' => 'AI failed to respond. Please try again later.',
            ], 500);
        }

        // Store in Database
        $chat = AiChatMessage::create([
            'user_id' => $user ? $user->id : null,
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
     * Get chat history for a session
     */
    public function getHistory(Request $request, $sessionId = null)
    {
        $user = $request->user();
        $sid = $sessionId ?? $request->session_id;

        $query = AiChatMessage::where('session_id', $sid);

        if ($user) {
            // Optionally enforce user ownership if strict
            $query->where('user_id', $user->id);
        }

        $history = $query->latest()->take(50)->get();

        return response()->json([
            'success' => true,
            'session_id' => $sid,
            'data' => $history,
        ]);
    }
}
