<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\JsonApi\Http\Middleware\ValidateJsonApiDocument;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('api/test-route', fn () => 'OK')
            ->middleware(ValidateJsonApiDocument::class);
    }

    /** @test */
    public function only_accepts_valid_json_api_document()
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertSuccessful();

        $this->patchJson('api/test-route', [
            'data' => [
                'id' => '1',
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertSuccessful();
    }

    /** @test */
    public function data_is_required()
    {
        $this->postJson('api/test-route', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test-route', [])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array()
    {
        $this->postJson('api/test-route', [
            'data' => 'string',
        ])->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test-route', [
            'data' => 'string',
        ])->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_type_is_required()
    {
        $this->postJson('api/test-route', [
            'data' => [
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'string',
                ],
            ],
        ])->assertSuccessful();
    }

    /** @test */
    public function data_type_must_be_a_string()
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attributes_is_required()
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_attributes_must_be_an_array()
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_id_is_required()
    {
        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function data_id_must_be_a_string()
    {
        $this->patchJson('api/test-route', [
            'data' => [
                'id' => 1,
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }
}
