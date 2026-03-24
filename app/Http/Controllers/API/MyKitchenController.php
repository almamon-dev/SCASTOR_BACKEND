<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeResource;
use App\Models\Favorite;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyKitchenController extends Controller
{
    use ApiResponse;

    /**
     * Get all recipes in the user's kitchen (favorites + saved AI recipes).
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Get standard favorite recipes
        $favoriteRecipes = Recipe::whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('category')->latest()->get();

        // 2. Get saved AI generated recipes
        $aiRecipes = \App\Models\AiGeneratedRecipe::where('user_id', $user->id)
            ->where('is_saved', true)
            ->latest()
            ->get();
        $formattedFavorites = RecipeResource::collection($favoriteRecipes)->resolve();

        $formattedFavorites = collect($formattedFavorites)->map(function ($item) {
            $item['source'] = 'app';

            return $item;
        });

        // 4. Format AI recipes & add source
        $formattedAiRecipes = $aiRecipes->map(function ($item) {
            $data = $item->toArray();
            $data['source'] = 'ai';
            $data['image'] = null;

            return $data;
        });

        // 5. Merge both collections
        $combined = $formattedFavorites->merge($formattedAiRecipes);

        return $this->sendResponse($combined, __('My Kitchen Recipes Fetched Successfully'));
    }

    /**
     * Save a recipe to My Kitchen.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
        ]);

        $user = Auth::user();

        // Check if already in kitchen
        $exists = Favorite::where('user_id', $user->id)
            ->where('recipe_id', $request->recipe_id)
            ->exists();

        if ($exists) {
            return $this->sendError(__('Recipe is already in My Kitchen'));
        }

        Favorite::create([
            'user_id' => $user->id,
            'recipe_id' => $request->recipe_id,
        ]);

        return $this->sendResponse([], __('Recipe saved to My Kitchen Successfully'));
    }

    /**
     * Remove a standard favorite recipe from My Kitchen.
     */
    public function removeFavorite($recipeId)
    {
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('recipe_id', $recipeId)
            ->first();

        if (! $favorite) {
            return $this->sendError(__('Recipe not found in My Kitchen'));
        }

        $favorite->delete();

        return $this->sendResponse([], __('Recipe removed from My Kitchen Successfully'));
    }

    /**
     * Remove an AI-generated recipe from My Kitchen.
     */
    public function removeAiRecipe($id)
    {
        $user = Auth::user();

        $recipe = \App\Models\AiGeneratedRecipe::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $recipe) {
            return $this->sendError(__('AI Recipe not found in My Kitchen'));
        }

        // Sets is_saved to false so it's no longer in the kitchen list
        $recipe->update(['is_saved' => false]);
        
        return $this->sendResponse([], __('AI Recipe removed from My Kitchen Successfully'));
    }
}


