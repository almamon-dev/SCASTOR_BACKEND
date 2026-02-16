<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate to avoid duplicate entry errors on fresh seed or check existence
        // Category::truncate(); // Be careful with foreign keys. 
        // Better to use updateOrCreate or just create if fresh --seed is used.

        $categories = [
            ['name' => 'All purpose Seasoning', 'icon' => 'leaf'],
            ['name' => 'Marinade', 'icon' => 'pizza'], // Using pizza as requested/implied western style
            ['name' => 'Quick Meal', 'icon' => 'zap'],
            ['name' => 'Western', 'icon' => 'croissant'], // Added Western
            ['name' => 'Veg', 'icon' => 'carrot'], // Added Veg
            ['name' => 'Seafood', 'icon' => 'fish'], // Updated to fish? or keep crab.
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'status' => true,
                ]
            );
        }
    }
}
