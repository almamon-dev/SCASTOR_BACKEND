<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key') ?? env('OPEN_API_KEY');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
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

            throw new \Exception('OpenAI API Key is missing in system settings.');
        }

        $systemPrompt = 'You are "CASTORS AI Chef", a helpful and friendly culinary assistant. You help users with cooking tips, recipe ideas, and kitchen advice using CASTORS seasonings. Keep your answers concise yet helpful.';

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($history as $msg) {
            if (isset($msg['role'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content'],
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ]);

            if ($response->failed()) {
                Log::error('AiChatService API Error: '.$response->body());

                throw new \Exception('OpenAI API Error: '.$response->body());
            }

            return $response->json('choices.0.message.content');

        } catch (\Exception $e) {
            Log::error('AiChatService Exception: '.$e->getMessage());

            throw $e;
        }
    }
}

