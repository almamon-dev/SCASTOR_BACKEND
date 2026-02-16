<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'prep_time',
        'meal_types',
        'image',
        'ingredients',
        'instructions',
        'status',
    ];

    protected $casts = [
        'meal_types' => 'array',
        'ingredients' => 'array',
        'instructions' => 'array',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
