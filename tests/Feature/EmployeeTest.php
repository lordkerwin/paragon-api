<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

class EmployeeTest extends TestCase
{

    use RefreshDatabase, WithFaker;
    private $admin;

    public function setup(): void
    {
        parent::setup();
        Artisan::call('passport:install');
        $this->seed('EmployeeTypeSeeder');
        $this->admin = User::factory()->create([
            'admin' => true
        ]);
    }

    public function test_can_create_employee()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        $response = $this->post(route('employees.store'), [
            'name' => $name = $this->faker->name(),
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'name',
                'key',
                'active',
                'department',
                'role',
                'employee_type',
                'updated_at',
                'created_at',
                'id'
            ],
            'meta' => [
                'success',
                'message',
            ]
        ]);
        $this->assertDatabaseHas('employees', [
            'name' => $name
        ]);
    }

    public function test_can_update_employee()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        $employee = Employee::factory()->create([
            'name' => $this->faker->name
        ]);

        $role = Role::factory()->create();
        $department = Department::factory()->create();

        $employee_type = EmployeeType::all()->first();

        $response = $this->patch(route('employees.update', $employee->id), [
            'name' => $new_name = $this->faker->name(),
            'role_id' => $role->id,
            'department_id' => $department->id,
            'employee_type_id' => $employee_type->id
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'name',
                'key',
                'active',
                'department' => [
                    'id',
                    'name'
                ],
                'role' => [
                    'id',
                    'name'
                ],
                'employee_type' => [
                    'id',
                    'name'
                ],
                'updated_at',
                'created_at',
                'id'
            ],
            'meta' => [
                'success',
                'message',
            ]
        ]);
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'name' => $new_name,
            'role_id' => $role->id,
            'department_id' => $department->id,
            'employee_type_id' => $employee_type->id
        ]);
    }

    public function test_can_make_employee_inactive()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);
        $role = Role::factory()->create();
        $department = Department::factory()->create();
        $employee_type = EmployeeType::all()->first();
        $employee = Employee::factory()->create([
            'name' => $name = $this->faker->name,
            'role_id' => $role->id,
            'department_id' => $department->id,
            'employee_type_id' => $employee_type->id
        ]);
        $response = $this->patch(route('employees.update', $employee->id), [
            'active' => false
        ]);
        $response->assertSuccessful();
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'active' => false
        ]);
    }

    public function test_can_make_employee_active()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);
        $role = Role::factory()->create();
        $department = Department::factory()->create();
        $employee_type = EmployeeType::all()->first();
        $employee = Employee::factory()->create([
            'name' => $name = $this->faker->name,
            'role_id' => $role->id,
            'department_id' => $department->id,
            'employee_type_id' => $employee_type->id,
            'active' => false
        ]);
        $response = $this->patch(route('employees.update', $employee->id), [
            'active' => true
        ]);
        $response->assertSuccessful();
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'active' => true
        ]);
    }

    public function test_can_delete_an_employee()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);
        $role = Role::factory()->create();
        $department = Department::factory()->create();
        $employee_type = EmployeeType::all()->first();
        $employee = Employee::factory()->create([
            'name' => $name = $this->faker->name,
            'role_id' => $role->id,
            'department_id' => $department->id,
            'employee_type_id' => $employee_type->id,
            'active' => true
        ]);
        $response = $this->delete(route('employees.destroy', $employee->id));
        $response->assertSuccessful();
        $this->assertSoftDeleted('employees', [
            'id' => $employee->id,
            'active' => false
        ]);
    }
}
