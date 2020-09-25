<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\PayRate;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeePayRateTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_pay_rate_to_employee()
    {
        $this->withoutExceptionHandling();
        $employee = Employee::factory()->create();
        $pay_rate = PayRate::factory()->create([
            'rate' => $rate = $this->faker->randomFloat(2, 5, 10)
        ]);

        $employee->payRates()->attach($pay_rate->id, [
            'from' => $from = Carbon::now()->subDays(7)->startOfDay(),
            'to' => $to = Carbon::now()->addDays(7)->startOfDay(),
        ]);
        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate->id,
            'from' => $from,
            'to' => $to
        ]);
        $this->assertTrue($employee->payRates[0]->rate == $rate);
    }

    public function test_employee_multiple_pay_rates()
    {
        $this->withoutExceptionHandling();
        $employee = Employee::factory()->create();

        $pay_rate_one = PayRate::factory()->create([
            'rate' => $rate_one = $this->faker->randomFloat(2, 5, 10)
        ]);
        $pay_rate_two = PayRate::factory()->create([
            'rate' => $rate_two = $this->faker->randomFloat(2, 5, 10)
        ]);
        $pay_rate_three = PayRate::factory()->create([
            'rate' => $rate_three = $this->faker->randomFloat(2, 5, 10)
        ]);
        // create the first pivot for a payrate that was 30 days ago and expired 7 days ago
        $employee->payRates()->attach($pay_rate_one->id, [
            'from' => $thirty_days_ago = Carbon::now()->subDays(30)->startOfDay(),
            'to' => $seven_days_ago = Carbon::now()->subDays(7)->startOfDay(),
        ]);
        // create the second pivot for a payrate that was 7 days ago and expires in 30 days
        $employee->payRates()->attach($pay_rate_two->id, [
            'from' => $seven_days_ago,
            'to' => $thirty_days_future = Carbon::now()->addDay(30)->startOfDay()
        ]);
        // create the third pivot for a payrate that starts 30 days from now and doesn't expire
        $employee->payRates()->attach($pay_rate_three->id, [
            'from' => $thirty_days_future,
            'to' => null
        ]);


        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_one->id,
            'from' => $thirty_days_ago,
            'to' => $seven_days_ago
        ]);
        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_two->id,
            'from' => $seven_days_ago,
            'to' => $thirty_days_future
        ]);
        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_three->id,
            'from' => $thirty_days_future,
            'to' => null
        ]);

        $current_payrate = $employee->getCurrentPayRate()->first();


        $this->assertTrue($current_payrate->rate == $rate_two);
    }
}
