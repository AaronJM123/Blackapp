<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_articles()
    {
        $this->postJson(route('api.v1.articles.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('articles', 0);
    }

    /** @test */
    public function can_create_articles()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        Sanctum::actingAs($user, ['article:create']);

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
            '_relationships' => [
                'category' => $category,
                'author' => $user,
            ],
        ])->assertCreated();

        $article = Article::first();

        $response->assertJsonApiResource($article, [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Nuevo artículo',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nue',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'content' => 'Contenido del artículo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Sanctum::actingAs(User::factory()->create());

        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => $article->slug,
            'content' => 'Contenido del artículo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => '$%^&',
            'content' => 'Contenido del artículo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'with_underscores',
            'content' => 'Contenido del artículo',
        ])->assertSee(trans('validation.no_underscores', [
            'attribute' => 'data.attributes.slug',
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => '-starts-with-dashes',
            'content' => 'Contenido del artículo',
        ])->assertSee(trans('validation.no_starting_dashes', [
            'attribute' => 'data.attributes.slug',
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'end-with-dashes-',
            'content' => 'Contenido del artículo',
        ])->assertSee(trans('validation.no_ending_dashes', [
            'attribute' => 'data.attributes.slug',
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo',
        ])->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function category_relationship_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
        ])->assertJsonApiValidationErrors('relationships.category');
    }

    /** @test */
    public function category_must_exist_in_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
            '_relationships' => [
                'category' => Category::factory()->make(),
            ],
        ])->assertJsonApiValidationErrors('relationships.category');
    }
}
