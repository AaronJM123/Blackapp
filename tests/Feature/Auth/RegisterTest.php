<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiHelpers();
    }

    /** @test */
    public function can_register()
    {
        $response = $this->postJson(
            route('api.v1.register'),
            $data = $this->validCredentials()
        );

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain token is invalid'
        );

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_register_again()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.register'))->assertNoContent();
    }

    /** @test */
    public function name_is_required()
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'name' => '',
        ]))->assertJsonValidationErrorFor('name');
    }

    /** @test */
    public function email_is_required()
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'email' => '',
        ]))->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_valid()
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'email' => 'invalidemail',
        ]))->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_unique()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'email' => $user->email,
        ]))->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function password_is_required()
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'password' => '',
        ]))->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'password' => 'password',
            'password_confirmation' => 'not-confirmed',
        ]))->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function device_name_is_required()
    {
        $this->postJson(route('api.v1.register'), $this->validCredentials([
            'device_name' => '',
        ]))->assertJsonValidationErrorFor('device_name');
    }

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'name' => 'Jorge García',
            'email' => 'jorge@aprendible.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'My device',
        ], $overrides);
    }
}
