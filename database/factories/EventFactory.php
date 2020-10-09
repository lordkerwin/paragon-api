<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'event_type_id' => 1,
            'employee_id' => 1,
            'processed' => false,
            'created_at' => $date = Carbon::now()->subHours($this->faker->randomDigit),
            'updated_at' => $date
        ];
    }
}
