<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\CommentAuthorController;
use App\Http\Controllers\Api\CommentArticleController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleCommentsController;
use App\JsonApi\Http\Middleware\ValidateJsonApiHeaders;
use App\JsonApi\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Controllers\Api\AuthenticatedUserController;

Route::apiResource('articles', ArticleController::class);
Route::apiResource('comments', CommentController::class);
Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');
Route::apiResource('authors', AuthorController::class)
    ->only('index', 'show');
Route::prefix('comments/{comment}')->group(function () {
    Route::controller(CommentArticleController::class)
        ->group(function () {
            Route::get('relationships/article', 'index')
                ->name('comments.relationships.article');

            Route::get('article', 'show')->name('comments.article');

            Route::patch('relationships/article', 'update');
        });

    Route::controller(CommentAuthorController::class)->group(function () {
        Route::get('relationships/author', 'index')
            ->name('comments.relationships.author');

        Route::patch('relationships/author', 'update');

        Route::get('author', 'show')
            ->name('comments.author');
    });
});

Route::prefix('articles/{article}')->group(function () {
    Route::controller(ArticleCategoryController::class)->group(function () {
        Route::get('relationships/category', 'index')
            ->name('articles.relationships.category');

        Route::patch('relationships/category', 'update');

        Route::get('category', 'show')
            ->name('articles.category');
    });

    Route::controller(ArticleAuthorController::class)->group(function () {
        Route::get('relationships/author', 'index')
            ->name('articles.relationships.author');

        Route::patch('relationships/author', 'update');

        Route::get('author', 'show')
            ->name('articles.author');
    });

    Route::controller(ArticleCommentsController::class)->group(function () {
        Route::get('relationships/comments', 'index')
            ->name('articles.relationships.comments');

        Route::patch('relationships/comments', 'update');

        Route::get('comments', 'show')->name('articles.comments');
    });
});

Route::withoutMiddleware([
    ValidateJsonApiDocument::class,
    ValidateJsonApiHeaders::class,
])->group(function () {
    Route::get('user', AuthenticatedUserController::class)
        ->middleware('auth:sanctum')
        ->name('auth.user');
    Route::post('login', LoginController::class)->name('login');
    Route::post('logout', LogoutController::class)->name('logout');
    Route::post('register', RegisterController::class)->name('register');
});
