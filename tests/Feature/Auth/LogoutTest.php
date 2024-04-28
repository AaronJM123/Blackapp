<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiHelpers();
    }

    /** @test */
    public function can_logout()
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$accessToken)
            ->postJson(route('api.v1.logout'))
            ->assertNoContent();

        $this->assertNull(PersonalAccessToken::findToken($accessToken));
    }
}
