<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'user_id',
        'meal_type',
        'scheduled_for',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
