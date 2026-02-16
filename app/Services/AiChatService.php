<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
        $this->model = config('services.openai.model', 'gpt-3.5-turbo');
    }

    /**
     * Send a message to the AI and get a response.
     * Optionally includes chat history for context.
     *
     * @param  array  $history  Previous messages [['role' => 'user', 'content' => '...'], ...]
     */
    public function sendMessage(string $message, array $history = []): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('AiChatService: API Key is missing.');

            return 'System Error: AI service is currently unavailable.';
        }

        // Construct the messages array
        $messages = [
            ['role' => 'system', 'content' => 'You are "CASTORS AI Chef", a helpful and friendly culinary assistant. You help users with cooking tips, recipe ideas, and kitchen advice using CASTORS seasonings. Keep your answers concise yet helpful.'],
        ];

        // Append history if valid
        if (! empty($history)) {
            $messages = array_merge($messages, $history);
        }

        // Append current user message
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                Log::error('AiChatService API Error: '.$response->body());

                return null;
            }

            return $response->json('choices.0.message.content');

        } catch (\Exception $e) {
            Log::error('AiChatService Exception: '.$e->getMessage());

            return null;
        }
    }
}
