<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_delete_articles()
    {
        $article = Article::factory()->create();

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );
    }

    /** @test */
    public function can_delete_owned_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author, ['article:delete']);

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertNoContent();

        $this->assertDatabaseCount('articles', 0);
    }

    /** @test */
    public function cannot_delete_articles_owned_by_other_users()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertForbidden();

        $this->assertDatabaseCount('articles', 1);
    }
}
