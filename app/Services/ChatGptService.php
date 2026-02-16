<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGptService
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
     * Generate a culinary response based on ingredients
     */
    public function generateRecipe(array $ingredients): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('ChatGptService: API Key is missing.');

            return null;
        }

        $ingredientList = implode(', ', $ingredients);
        $prompt = "Create a unique and delicious recipe using these primary ingredients: [{$ingredientList}]. 
        Ensure 'CASTORS Seasoning' is used.
        
        Strictly return ONLY a valid JSON object with no markdown formatting. The structure must be:
        {
            \"title\": \"Recipe Name (start with CASTORS)\",
            \"description\": \"Brief appetizing description\",
            \"prep_time\": \"Estimated time in minutes (e.g. 25)\",
            \"ingredients\": [\"Quantity + Ingredient 1\", \"Quantity + Ingredient 2\"],
            \"instructions\": [\"Step 1 instruction (NO numbering)\", \"Step 2 instruction (NO numbering)\"]
        }";
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional chef assistant that outputs strictly valid JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    // 'response_format' => ['type' => 'json_object'] // Enable if model supports it
                ]);

            if ($response->failed()) {
                Log::error('ChatGptService Error: '.$response->body());

                return null;
            }

            $content = $response->json('choices.0.message.content');

            // Clean up code blocks if present (```json ... ```)
            $content = preg_replace('/^```json\s*|\s*```$/', '', $content);

            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('ChatGptService JSON Decode Error: '.json_last_error_msg());

                return null;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('ChatGptService Exception: '.$e->getMessage());

            return null;
        }
    }
}
