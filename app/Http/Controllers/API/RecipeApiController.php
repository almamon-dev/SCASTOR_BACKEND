<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RecipeResource;
use App\Models\Category;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RecipeApiController extends Controller
{
    use ApiResponse;

    /**
     * Get both categories and latest recipes for the home screen.
     */
    public function home(Request $request)
    {
        // 1. Get latest 5 categories for the slider
        $categories = Category::where('status', 1)->latest()->take(5)->get();

        // 2. Start recipe query
        $recipeQuery = Recipe::with('category')->where('status', 1);

        // 3. Filter if requested
        $categoryId = $request->query('category_id');
        $categorySlug = $request->query('category_slug');

        if ($categoryId) {
            $recipeQuery->where('category_id', $categoryId);
        } elseif ($categorySlug) {
            $recipeQuery->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // 4. If filtering by category, get ALL recipes. Otherwise, take only 3.
        if ($categoryId || $categorySlug) {
            $recipes = $recipeQuery->latest()->get();
        } else {
            $recipes = $recipeQuery->latest()->take(3)->get();
        }

        return $this->sendResponse([
            'categories' => CategoryResource::collection($categories),
            'recipes' => RecipeResource::collection($recipes),
        ], __('Data Fetched Successfully'));
    }

    /**
     * Get single recipe details.
     */
    public function recipeDetails($slug)
    {
        $recipe = Recipe::with('category')->where('slug', $slug)->where('status', true)->firstOrFail();

        return new RecipeResource($recipe);
    }
}
