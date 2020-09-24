<?php

namespace Tests\Feature;

use App\Models\PayRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PayRateTest extends TestCase
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

    public function test_can_get_all_pay_rates()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        PayRate::factory()->count(20)->create();

        $response = $this->get(route('payrates.index'));
        dd($response);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'rate',
                    'deleted_at',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'path',
                'per_page',
                'to',
                'total',
                'success',
                'message',
            ]
        ]);
    }

    public function test_can_create_pay_rate()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        $response = $this->post(route('payrates.store'), [
            'rate' => $rate = $this->faker->numberBetween(8, 14),
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'meta' => [
                'success' => true,
                'message' => 'payrate created'
            ],
            'data' => [
                'rate' => $rate
            ]
        ]);
    }

    public function test_cannot_create_pay_rate_greater_than_fifty()
    {
        $this->withoutExceptionHandling();
        Passport::actingAs($this->admin);

        $response = $this->post(route('payrates.store'), [
            'rate' => $rate = $this->faker->numberBetween(51, 60),
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('pay_rates', [
            'rate' => $rate
        ]);
    }
}
