<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\JsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    use JsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'body' => $this->resource->body,
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['article', 'author'];
    }
}
