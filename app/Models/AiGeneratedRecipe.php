<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiGeneratedRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'prep_time',
        'ingredients',
        'instructions',
        'is_saved',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'is_saved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
