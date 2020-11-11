<?php

namespace Database\Factories;

use App\Models\Shift;
use App\Models\ShiftStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shift::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'employee_id' => 1,
            'start' => $start = Carbon::now()->subHours(8),
            'end' => $end = Carbon::now(),
            'pay_rate' => $rate = $this->faker->randomFloat(2, 8, 10),
            'duration' => $duration = $start->diffInHours($end),
            'value' => $duration * $rate,
            'shift_status_id' => ShiftStatus::retrieve('new', 'id'),
            'processed' => false,
            'payroll_locked' => false
        ];
    }


}
