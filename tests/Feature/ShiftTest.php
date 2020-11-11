<?php

namespace Tests\Feature;

use App\Http\Controllers\ShiftController;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventType;
use App\Models\PayRate;
use App\Models\ShiftStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setup(): void
    {
        parent::setup();
    }

    private function createEmployee()
    {
        $employee = Employee::factory()->create();
        $pay_rate = PayRate::factory()->create([
            'rate' => $this->faker->randomFloat(2, 5, 10)
        ]);
        $employee->payRates()->attach($pay_rate->id, [
            'from' => Carbon::now()->subDays(30)->startOfDay(),
            'to' => null
        ]);


        return $employee;
    }

    public function test_can_init_shift_for_employee()
    {
        $this->withoutExceptionHandling();
        $employee = $this->createEmployee();
        $this->seed('ShiftStatusSeeder');

        // create the events
        EventType::factory()->create(['name' => 'clock-in']);
        EventType::factory()->create(['name' => 'clock-out']);
        EventType::factory()->create(['name' => 'off-site']);
        EventType::factory()->create(['name' => 'on-site']);

        $clock_in_event = Event::factory()->create([
            'event_type_id' => EventType::retrieve('clock-in', 'id'),
            'employee_id' => $employee->id,
            'created_at' => Carbon::yesterday()->subHours(4),
        ]);

        $clock_out_event = Event::factory()->create([
            'event_type_id' => EventType::retrieve('clock-out', 'id'),
            'employee_id' => $employee->id,
            'created_at' => Carbon::yesterday()->subHours(1),
        ]);

        $shift_controller = new ShiftController();
        $shift_controller->createShift($clock_in_event, $clock_out_event);
        $this->assertDatabaseHas('shifts', [
            'employee_id' => $employee->id,
            'start' => $clock_in_event->created_at,
            'end' => $clock_out_event->created_at,
            'shift_status_id' => ShiftStatus::retrieve('new', 'id'),
            'processed' => false,
            'pay_rate' => $employee->getCurrentPayRate($clock_in_event->created_at)->rate
        ]);
    }
}
