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

    public function test_employee_with_three_pay_rates_set()
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


        $current_pay_rate = $employee->getCurrentPayRate();
        $this->assertTrue($current_pay_rate->rate == $rate_two);
    }

    public function test_employee_with_single_pay_rate_set()
    {
        $this->withoutExceptionHandling();
        $employee = Employee::factory()->create();

        $pay_rate_one = PayRate::factory()->create([
            'rate' => $rate_one = $this->faker->randomFloat(2, 5, 10)
        ]);

        // create the first pivot for a payrate that was 30 days ago and no expiration
        $employee->payRates()->attach($pay_rate_one->id, [
            'from' => $thirty_days_ago = Carbon::now()->subDays(30)->startOfDay(),
            'to' => null
        ]);


        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_one->id,
            'from' => $thirty_days_ago,
            'to' => null
        ]);


        $current_pay_rate = $employee->getCurrentPayRate();
        $this->assertTrue($current_pay_rate->rate == $rate_one);
    }

    public function test_employee_with_two_pay_rates_set()
    {
        $this->withoutExceptionHandling();
        $employee = Employee::factory()->create();

        $pay_rate_one = PayRate::factory()->create([
            'rate' => $rate_one = $this->faker->randomFloat(2, 5, 10)
        ]);
        $pay_rate_two = PayRate::factory()->create([
            'rate' => $rate_two = $this->faker->randomFloat(2, 5, 10)
        ]);

        // create the first pivot for a pay rate that was 30 days ago and expires in 7 days
        $employee->payRates()->attach($pay_rate_one->id, [
            'from' => $thirty_days_ago = Carbon::now()->subDays(30)->startOfDay(),
            'to' => $seven_days_ahead = Carbon::now()->addDays(7)->startOfDay(),
        ]);
        // create the second pivot for a pay rate that starts in 7 days and doesnt expire
        $employee->payRates()->attach($pay_rate_two->id, [
            'from' => $seven_days_ahead,
            'to' => null
        ]);

        // from the data above, if we got the pay rate 'now', it would be the first one (pay rate one)
        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_one->id,
            'from' => $thirty_days_ago,
            'to' => $seven_days_ahead
        ]);
        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_two->id,
            'from' => $seven_days_ahead,
            'to' => null
        ]);


        $current_pay_rate = $employee->getCurrentPayRate();
        $this->assertTrue($current_pay_rate->rate == $rate_one);
    }
}
