<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeEventTest extends TestCase
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

    private function createEmployee()
    {
        return Employee::factory()->create();
    }


    public function test_employee_available_events()
    {
        // create the events
        EventType::factory()->create(['name' => 'clock-in']);
        EventType::factory()->create(['name' => 'clock-out']);
        EventType::factory()->create(['name' => 'off-site']);
        EventType::factory()->create(['name' => 'on-site']);

        $employee = $this->createEmployee();
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

        // Last event should be a clock-out
        $last_event = $employee->getLastEvent();
        $this->assertTrue($last_event->event_type_id == EventType::retrieve('clock-out', 'id'));
        // there should only be one event available and its a clock-in
        $available_events = $employee->getAvailableEvents();
        $this->assertTrue($available_events[0]->name == 'clock-in');
    }


    public function test_employee_first_scan_event()
    {
        $this->withExceptionHandling();
        // create the events
        EventType::factory()->create(['name' => 'clock-in']);
        EventType::factory()->create(['name' => 'clock-out']);
        EventType::factory()->create(['name' => 'off-site']);
        EventType::factory()->create(['name' => 'on-site']);

        // The first scan event is the event where the employee would essentially scan a clocking in card.
        // we expect to get back from that, the employee name/id, the available events, and the last event.
        $employee = $this->createEmployee();
        $response = $this->post(route('employees.scan'), [
            'key' => $employee->key
        ]);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'key',
                'active',
                'department',
                'role',
                'employee_type',
                'last_event',
                'available_events' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ],
            'meta' => [
                'success',
                'message'
            ]
        ]);
        $response->assertJsonFragment([
            'name' => 'clock-in'
        ]);
    }

    public function test_employee_can_create_a_clock_in_event() {

    }


}
