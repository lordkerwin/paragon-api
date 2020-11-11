<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\PayRate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EmployeePayRateTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    private $admin;

    public function setup(): void
    {
        parent::setup();
        $this->admin = User::factory()->create([
            'admin' => true
        ]);
    }

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
        // create the first pivot for a pay rate that was 30 days ago and expired 7 days ago
        $employee->payRates()->attach($pay_rate_one->id, [
            'from' => $thirty_days_ago = Carbon::now()->subDays(30)->startOfDay(),
            'to' => $seven_days_ago = Carbon::now()->subDays(7)->startOfDay(),
        ]);
        // create the second pivot for a pay rate that was 7 days ago and expires in 30 days
        $employee->payRates()->attach($pay_rate_two->id, [
            'from' => $seven_days_ago,
            'to' => $thirty_days_future = Carbon::now()->addDays(30)->startOfDay()
        ]);
        // create the third pivot for a pay rate that starts 30 days from now and doesn't expire
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

    public function test_employee_with_single_pay_rate_set()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        $employee = Employee::factory()->create();

        $pay_rate_one = PayRate::factory()->create([
            'rate' => $rate_one = $this->faker->randomFloat(2, 5, 10)
        ]);

        // create the first pivot for a pay rate that was 30 days ago and no expiration
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

    public function test_can_add_pay_rate_for_employee()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        $employee = Employee::factory()->create();
        $pay_rate = PayRate::factory()->create([
            'rate' => $rate_one = $this->faker->randomFloat(2, 5, 10)
        ]);

        $response = $this->post(route('employee-pay-rates.store'), [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate->id,
            'from' => $from = Carbon::now()->subMonths(3)->startOfDay(),
            'to' => $to = Carbon::now()->subMonths(2)->startOfDay(),
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate->id,
            'from' => $from,
            'to' => $to
        ]);
    }

    public function test_can_add_overlapping_pay_rates_for_employee()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        $employee = Employee::factory()->create();

        // create 3 payrates
        $pay_rate_one = PayRate::factory()->create([
            'rate' => $rate_one = $this->faker->randomFloat(2, 5, 10)
        ]);
        $pay_rate_two = PayRate::factory()->create([
            'rate' => $rate_two = $this->faker->randomFloat(2, 5, 10)
        ]);
        $pay_rate_three = PayRate::factory()->create([
            'rate' => $rate_three = $this->faker->randomFloat(2, 5, 10)
        ]);

        // Set the first payrate in the pivot to start 3 months ago, and end 2 months ago
        $employee->payRates()->attach($pay_rate_one->id, [
            'from' => $three_months_ago = Carbon::now()->subMonths(3)->startOfDay(),
            'to' => $two_months_ago = Carbon::now()->subMonths(2)->startOfDay(),
        ]);
        // set the second payrate in the pivot to start 2 months ago, and have no end,
        // this would be the "current" payrate so to speak.
        $employee->payRates()->attach($pay_rate_two->id, [
            'from' => $two_months_ago,
            'to' => null
        ]);

        // we're now going to create a new pay_rate that starts from tomorrow and have no end-date
        $response = $this->post(route('employee-pay-rates.store'), [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_three->id,
            'from' => $tomorrow = Carbon::now()->addDays(1)->startOfDay(),
            'to' => null
        ]);
        // what we're going to assert is that the payrate 2 that was the current, should now have an 'to' date of tomorrow
        // which is the start date of the 3rd payrate we've just posted to the endpoint.
        $response->assertSuccessful();

        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_one->id,
            'from' => $three_months_ago,
            'to' => $two_months_ago
        ]);

        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_two->id,
            'from' => $two_months_ago,
            'to' => $tomorrow
        ]);

        $this->assertDatabaseHas('employee_pay_rate', [
            'employee_id' => $employee->id,
            'pay_rate_id' => $pay_rate_three->id,
            'from' => $tomorrow,
            'to' => null
        ]);

        // lets pretend its 3 months ago, the payrate that that point should be whatever $rate_one is
        Carbon::setTestNow($three_months_ago);
        $current_pay_rate = $employee->getCurrentPayRate();
        $this->assertTrue($current_pay_rate->rate == $rate_one);

        // lets pretend its 2 months ago, the payrate that that point should be whatever $rate_two is
        Carbon::setTestNow($two_months_ago);
        $current_pay_rate = $employee->getCurrentPayRate();
        $this->assertTrue($current_pay_rate->rate == $rate_two);

        // lets pretend its tomorrow, the payrate that that point should be whatever $rate_three is
        Carbon::setTestNow($tomorrow);
        $current_pay_rate = $employee->getCurrentPayRate();
        $this->assertTrue($current_pay_rate->rate == $rate_three);
    }
}
