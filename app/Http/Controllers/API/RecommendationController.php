<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeResource;
use App\Models\RecommendationSchedule;
use App\Traits\ApiResponse;
use Carbon\Carbon;

class RecommendationController extends Controller
{
    use ApiResponse;

    /**
     * Get today's recommended meals.
     * Auto-generates if not already set.
     */
    public function today()
    {
        $today = Carbon::today()->toDateString();
        $mealTypes = ['breakfast', 'lunch', 'dinner'];
        $recommendations = [];
        $user = auth('sanctum')->user();

        foreach ($mealTypes as $type) {
            $schedule = [];

            // 1. Try to fetch Personalized Recommendation (if logged in)
            if ($user) {
                $schedule = RecommendationSchedule::where('scheduled_for', $today)
                    ->where('meal_type', $type)
                    ->where('user_id', $user->id)
                    ->first();
            }

            // 2. Fallback to Global Recommendation
            if (! $schedule) {
                $schedule = RecommendationSchedule::where('scheduled_for', $today)
                    ->where('meal_type', $type)
                    ->whereNull('user_id')
                    ->first();
            }

            $recommendations[$type] = ($schedule && $schedule->recipe)
                ? new RecipeResource($schedule->recipe)
                : [];
        }

        return $this->sendResponse($recommendations, __('Today\'s recommendations fetched successfully'));
    }
}
