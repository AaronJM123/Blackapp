<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_related_comments_of_an_article()
    {
        $article = Article::factory()->hasComments(2)->create();

        // articles/the-slug?include=comments
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'comments',
        ]);

        $response = $this->getJson($url);

        $response->assertJsonCount(2, 'included');

        $article->comments->map(fn ($comment) => $response->assertJsonFragment([
            'type' => 'comments',
            'id' => (string) $comment->getRouteKey(),
            'attributes' => [
                'body' => $comment->body,
            ],
        ]));
    }

    /** @test */
    public function can_include_related_comments_of_multiple_articles()
    {
        $article = Article::factory()->hasComments(2)->create();
        $article2 = Article::factory()->hasComments(2)->create();

        // articles?include=comments
        $url = route('api.v1.articles.index', [
            'include' => 'comments',
        ]);

        $response = $this->getJson($url);

        $response->assertJsonCount(4, 'included');
    }
}
