<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExceptionsHandlerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function json_api_errors_are_only_shown_to_requests_with_the_prefix_api()
    {
        $this->getJson('api/route')
            ->assertJsonApiError(
                detail: 'The route api/route could not be found.'
            );

        $this->getJson('api/route')
            ->assertJsonApiError(
                detail: 'The route api/route could not be found.',
            );
    }

    /** @test */
    public function default_Laravel_error_is_shown_to_requests_outside_the_prefix_api()
    {
        $this->getJson('non/api/route')
            ->assertJson([
                'message' => 'The route non/api/route could not be found.',
            ]);

        $this->withoutJsonApiHeaders()
            ->getJson('non/api/route')
            ->assertJson([
                'message' => 'The route non/api/route could not be found.',
            ]);
    }
}
