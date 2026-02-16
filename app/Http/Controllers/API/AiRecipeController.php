<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ChatGptService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AiRecipeController extends Controller
{
    use ApiResponse;

    protected $chatGptService;

    public function __construct(ChatGptService $chatGptService)
    {
        $this->chatGptService = $chatGptService;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'ingredients' => ['required', function ($attribute, $value, $fail) {
                if (! is_string($value) && ! is_array($value)) {
                    $fail('The '.$attribute.' must be a string or an array.');
                }
            }],
        ]);

        $ingredients = is_array($request->ingredients)
            ? $request->ingredients
            : array_map('trim', explode(',', $request->ingredients));

        $recipeData = $this->chatGptService->generateRecipe($ingredients);

        if (! $recipeData) {
            return $this->sendError('Failed to generate recipe. Please try again.', [], 500);
        }

        // Store in AiGeneratedRecipe Table
        try {
            $user = $request->user();
            if (! $user) {
                \Illuminate\Support\Facades\Log::error('AI Recipe Save Error: User not authenticated.');

                return $this->sendResponse($recipeData, 'Recipe generated (Login to save history).');
            }

            $prepTime = (int) filter_var($recipeData['prep_time'], FILTER_SANITIZE_NUMBER_INT);

            $aiRecipe = \App\Models\AiGeneratedRecipe::create([
                'user_id' => $user->id,
                'title' => $recipeData['title'],
                'slug' => \Illuminate\Support\Str::slug($recipeData['title']).'-'.time(),
                'description' => $recipeData['description'],
                'prep_time' => $prepTime > 0 ? $prepTime : 30,
                'ingredients' => $recipeData['ingredients'],
                'instructions' => $recipeData['instructions'],
                'is_saved' => false,
            ]);

            return $this->sendResponse($aiRecipe, 'Recipe generated successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Recipe Save Error: '.$e->getMessage());
            \Illuminate\Support\Facades\Log::error('AI Recipe Data: '.json_encode($recipeData));

            return $this->sendResponse($recipeData, 'Recipe generated (but failed to save to history: '.$e->getMessage().')');
        }
    }

    /**
     * Get details of an AI generated recipe
     *
     * @param  string  $idOrSlug
     */
    public function show($idOrSlug, Request $request)
    {
        $user = $request->user();

        $recipe = \App\Models\AiGeneratedRecipe::where(function ($query) use ($idOrSlug) {
            $query->where('id', $idOrSlug)
                ->orWhere('slug', $idOrSlug);
        })
            ->where('user_id', $user->id)
            ->first();

        if (! $recipe) {
            return $this->sendError('Recipe not found or access denied.', [], 404);
        }

        return $this->sendResponse($recipe, 'Recipe details fetched successfully.');
    }

    /**
     * Save AI generated recipe to My Kitchen
     */
    public function storeToKitchen($id)
    {
        $user = request()->user();
        $recipe = \App\Models\AiGeneratedRecipe::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $recipe) {
            return $this->sendError('Recipe not found or access denied.', [], 404);
        }

        $recipe->update(['is_saved' => true]);

        return $this->sendResponse($recipe, 'Recipe saved to My Kitchen successfully.');
    }
}
