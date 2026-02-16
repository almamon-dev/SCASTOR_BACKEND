<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
        $selectedMealTypes = fake()->randomElements($mealTypes, rand(1, 3));
        $title = fake()->sentence(3);

        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'prep_time' => rand(15, 90),
            'meal_types' => $selectedMealTypes,
            'image' => null, // Or fake()->imageUrl()
            'ingredients' => [
                fake()->word(),
                fake()->word(),
                fake()->word(),
                fake()->word(),
                fake()->word(),
            ],
            'instructions' => [
                fake()->sentence(),
                fake()->sentence(),
                fake()->sentence(),
            ],
            'status' => true,
        ];
    }
}
