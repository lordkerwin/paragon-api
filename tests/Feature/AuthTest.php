<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_admin_user_can_login()
    {
        $this->withoutExceptionHandling();
        Artisan::call('passport:install');
        $user = User::factory()->create([
            'admin' => true,
            'email' => $email = $this->faker->safeEmail,
            'password' => Hash::make('password')
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => [
                'success',
                'message'
            ],
            'data' => [
                'token',
                'user' => [
                    'name',
                    'email'
                ]
            ]
        ]);
        $response->assertJsonFragment([
            'token' => $response['data']['token']
        ]);
    }

    public function test_an_admin_user_can_logout()
    {
        $this->withoutExceptionHandling();
        // install passport
        Artisan::call('passport:install');
        $user = User::factory()->create([
            'admin' => true,
            'email' => $email = $this->faker->safeEmail,
            'password' => Hash::make('password')
        ]);

        Passport::actingAs($user);
        $response = $this->get(route('auth.logout'));
        $response->assertStatus(200);
        $response->assertJson([
            'meta' => [
                'success' => true,
                'message' => 'You have been successfully logged out!'
            ],
            'data' => null
        ]);
    }

    public function test_a_non_admin_user_cannot_login()
    {
        $this->withoutExceptionHandling();
        // install passport
        Artisan::call('passport:install');
        User::factory()->create([
            'admin' => false,
            'email' => $email = $this->faker->safeEmail,
            'password' => Hash::make('password')
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertForbidden();
        $response->assertJsonStructure([
            'meta' => [
                'success',
                'message'
            ],
            'data'
        ]);
        $response->assertJsonFragment([
            'message' => 'Forbidden'
        ]);
    }

    // TODO: Add Registration Routes

}
