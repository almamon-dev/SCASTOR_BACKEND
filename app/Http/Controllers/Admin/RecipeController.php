<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with('category');

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%")
                ->orWhere('slug', 'like', "%{$request->search}%");
        }

        return Inertia::render('Admin/Recipes/Index', [
            'recipes' => $query->latest()->paginate($request->per_page ?? 15)->withQueryString(),
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Recipes/Create', [
            'categories' => Category::where('status', true)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prep_time' => 'nullable|string|max:255',
            'meal_types' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ingredients' => 'nullable|array',
            'instructions' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            $validated['image'] = Helper::uploadFile('recipes', $request->file('image'));
        }

        Recipe::create($validated);

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe created successfully.');
    }

    public function edit(Recipe $recipe)
    {
        return Inertia::render('Admin/Recipes/Edit', [
            'recipe' => $recipe,
            'categories' => Category::where('status', true)->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prep_time' => 'nullable|string|max:255',
            'meal_types' => 'nullable|array',
            'image' => 'nullable',
            'ingredients' => 'nullable|array',
            'instructions' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            Helper::deleteFile($recipe->image);
            $validated['image'] = Helper::uploadFile('recipes', $request->file('image'));
        } else {
            unset($validated['image']);
        }

        $recipe->update($validated);

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe)
    {
        if ($recipe->image) {
            Helper::deleteFile($recipe->image);
        }
        $recipe->delete();

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe deleted successfully.');
    }
}
