<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\User;
use Inertia\Inertia;

class OverviewController extends Controller
{
    public function index()
    {
        // Counts
        $recipesCount = Recipe::count();
        $categoriesCount = Category::count();
        $usersCount = User::count();

        // Recent Recipes
        $recentRecipes = Recipe::with('category')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'category' => $recipe->category ? $recipe->category->name : 'Uncategorized',
                    'created_at' => $recipe->created_at->format('M d, Y'),
                    'initials' => substr($recipe->title, 0, 2),
                    'color' => 'bg-indigo-100 text-indigo-600', // Default color for now
                    'status' => $recipe->status ? 'Active' : 'Inactive',
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'recipes' => [
                    'count' => $recipesCount,
                    'status' => '+ACTIVE',
                ],
                'categories' => [
                    'count' => $categoriesCount,
                    'status' => '+TYPES',
                ],
                'users' => [
                    'count' => $usersCount,
                    'status' => '+MEMBERS',
                ],

                'favorites' => [
                    'count' => 0,
                    'status' => '+LIKES',
                ],
            ],
            'recentRecipes' => $recentRecipes,
            'portfolioHealth' => [
                'completion' => 100,
                'activeExperiences' => 'High',
                'verifiedSkills' => 'Active',
            ],
            'openai_api_key' => env('OPENAI_API_KEY'),
        ]);
    }
}
