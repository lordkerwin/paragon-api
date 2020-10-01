<?php

namespace Database\Factories;

use App\Models\PayRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayRateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PayRate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'rate' => $this->faker->unique(false, 1000)->randomFloat(2, 5, 20)
        ];
    }
}
