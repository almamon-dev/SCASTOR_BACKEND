<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RecipeResource;
use App\Models\Category;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RecipeLibraryController extends Controller
{
    use ApiResponse;

    /**
     * Get all categories and recipes for the Recipe Library screen.
     */
    public function index(Request $request)
    {
        // 1. Get ALL active categories
        $categories = Category::where('status', 1)->latest()->get();

        // 2. Start recipe query
        $recipeQuery = Recipe::with('category')->where('status', 1);

        // 3. Filter by category if ID is provided
        if ($request->has('category_id')) {
            $recipeQuery->where('category_id', $request->category_id);
        }

        // 4. Return ALL matching recipes
        $recipes = $recipeQuery->latest()->get();

        return $this->sendResponse([
            'categories' => CategoryResource::collection($categories),
            'recipes' => RecipeResource::collection($recipes),
        ], __('Recipe Library Fetched Successfully'));
    }
}
