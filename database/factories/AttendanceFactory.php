<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = \App\Models\Attendance::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHours(8),
            'clock_out' => Carbon::now()->subHour(),
            'status' => 'completed',
            'remarks' => fake()->sentence(),
        ];
    }
}

