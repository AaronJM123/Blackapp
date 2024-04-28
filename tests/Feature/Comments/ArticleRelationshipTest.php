<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_article_identifier()
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.article', $comment);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'id' => $comment->article->getRouteKey(),
                'type' => 'articles',
            ],
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_article_resource()
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.article', $comment);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                'id' => $comment->article->getRouteKey(),
                'type' => 'articles',
                'attributes' => [
                    'title' => $comment->article->title,
                ],
            ],
        ]);
    }

    /** @test */
    public function can_update_the_associated_article()
    {
        $article = Article::factory()->create();

        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.article', $comment);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'articles',
                'id' => $article->getRouteKey(),
            ],
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => $article->getRouteKey(),
            ],
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'article_id' => $article->id,
        ]);
    }

    /** @test */
    public function article_must_exist_in_database()
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.article', $comment);

        $this->patchJson($url, [
            'data' => [
                'type' => 'articles',
                'id' => 'non-existing',
            ],
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'article_id' => $comment->article_id,
        ]);
    }
}
