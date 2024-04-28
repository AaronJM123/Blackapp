<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_delete_comments()
    {
        $comment = Comment::factory()->create();

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );
    }

    /** @test */
    public function can_delete_owned_comments()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ['comment:delete']);

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertNoContent();

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function cannot_delete_comments_owned_by_other_users()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
    }
}
