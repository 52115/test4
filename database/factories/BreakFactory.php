<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Break>
 */
class BreakFactory extends Factory
{
    protected $model = \App\Models\BreakTime::class;

    public function definition(): array
    {
        $breakStart = Carbon::now()->subHours(5);
        $breakEnd = $breakStart->copy()->addHour();
        
        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ];
    }
}

